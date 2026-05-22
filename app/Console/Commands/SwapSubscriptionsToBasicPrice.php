<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SwapSubscriptionsToBasicPrice extends Command
{
    /**
     * 実行例：
     *
     * dry-run:
     * php artisan subscriptions:swap-basic-price --dry-run
     *
     * 1ユーザーだけ実行:
     * php artisan subscriptions:swap-basic-price --user_id=41
     *
     * 全対象実行:
     * php artisan subscriptions:swap-basic-price
     */
    protected $signature = 'subscriptions:swap-basic-price
        {--dry-run : 実際には変更せず、対象だけ表示する}
        {--user_id= : 特定ユーザーIDだけ実行する}
        {--old_price= : 旧Stripe Price IDを上書き指定する}
        {--new_price= : 新Stripe Price IDを上書き指定する}
    ';

    protected $description = '既存の有効サブスクリプションを旧Priceから新Priceへ日割りなしで切り替えます';

    public function handle(): int
    {
        // .envのSTRIPE_PRICE_BASICを新PriceIDとして使用
        // 旧PriceIDはオプション必須（デフォルトなし）
        $defaultNewPriceId = env('STRIPE_PRICE_BASIC');
        $defaultOldPriceId = null; // 旧IDはハードコードしない。--old_price で必ず明示する

        $oldPriceId = (string) ($this->option('old_price') ?: $defaultOldPriceId);
        $newPriceId = (string) ($this->option('new_price') ?: $defaultNewPriceId);

        if ($oldPriceId === '' || $newPriceId === '') {
            $this->error('old_price または new_price が空です。--old_price=price_xxx で明示してください。');
            return self::FAILURE;
        }

        if ($oldPriceId === $newPriceId) {
            $this->error('old_price と new_price が同じです。');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $userId = $this->option('user_id');

        $this->info('旧Price: ' . $oldPriceId);
        $this->info('新Price: ' . $newPriceId);
        $this->info($dryRun ? 'モード: dry-run（変更なし）' : 'モード: 実行');

        $query = User::query()
            ->whereHas('subscriptions', function ($q) use ($oldPriceId) {
                $q->where('type', 'default')
                    ->where('stripe_price', $oldPriceId)
                    ->whereIn('stripe_status', ['active', 'trialing'])
                    ->where(function ($q2) {
                        $q2->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    });
            })
            ->with(['subscriptions' => function ($q) use ($oldPriceId) {
                $q->where('type', 'default')
                    ->where('stripe_price', $oldPriceId)
                    ->whereIn('stripe_status', ['active', 'trialing'])
                    ->orderByDesc('created_at');
            }]);

        if ($userId) {
            $query->where('id', (int) $userId);
        }

        $users = $query->get();

        $this->line('');
        $this->info('対象ユーザー数: ' . $users->count());

        if ($users->isEmpty()) {
            $this->warn('対象ユーザーがいません。');
            return self::SUCCESS;
        }

        foreach ($users as $user) {
            // with()でロード済みのリレーションから取得（追加クエリなし）
            $subscription = $user->subscriptions->first();

            $this->line(sprintf(
                'user_id=%s email=%s subscription=%s current_price=%s status=%s',
                $user->id,
                $user->email ?? '-',
                $subscription?->stripe_id ?? '-',
                $subscription?->stripe_price ?? '-',
                $subscription?->stripe_status ?? '-'
            ));
        }

        if ($dryRun) {
            $this->line('');
            $this->info('dry-run のため変更は行っていません。');
            return self::SUCCESS;
        }

        if (! $this->confirm("上記 {$users->count()} 件を新Priceへ切り替えます。実行しますか？")) {
            $this->warn('中止しました。');
            return self::SUCCESS;
        }

        $success = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($users as $user) {
            try {
                // with()でロード済みのリレーションから取得（追加クエリなし）
                $subscription = $user->subscriptions->first();

                if (! $subscription) {
                    $skipped++;
                    $this->warn("skip user_id={$user->id}: subscription not found");
                    continue;
                }

                if ($subscription->stripe_price !== $oldPriceId) {
                    $skipped++;
                    $this->warn("skip user_id={$user->id}: current price is not old price ({$subscription->stripe_price})");
                    continue;
                }

                if (! in_array($subscription->stripe_status, ['active', 'trialing'], true)) {
                    $skipped++;
                    $this->warn("skip user_id={$user->id}: status={$subscription->stripe_status}");
                    continue;
                }

                $oldPriceBeforeSwap = $subscription->stripe_price;
                $stripeSubscriptionId = $subscription->stripe_id;

                // 日割りなしでPrice差し替え
                $subscription->noProrate()->swap($newPriceId);

                // ローカルモデルを最新化
                $subscription->refresh();

                $success++;

                $this->info(sprintf(
                    'success user_id=%s email=%s subscription=%s old_price=%s new_price=%s db_price_after=%s',
                    $user->id,
                    $user->email ?? '-',
                    $stripeSubscriptionId,
                    $oldPriceBeforeSwap,
                    $newPriceId,
                    $subscription->stripe_price
                ));

                Log::info('Subscription price swapped to basic price', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'stripe_subscription_id' => $stripeSubscriptionId,
                    'old_price' => $oldPriceBeforeSwap,
                    'new_price' => $newPriceId,
                    'db_price_after' => $subscription->stripe_price,
                ]);
            } catch (\Throwable $e) {
                $failed++;

                $this->error(sprintf(
                    'failed user_id=%s email=%s error=%s',
                    $user->id,
                    $user->email ?? '-',
                    $e->getMessage()
                ));

                Log::error('Subscription price swap to basic price failed', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'old_price' => $oldPriceId,
                    'new_price' => $newPriceId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->line('');
        $this->info("完了 success={$success} failed={$failed} skipped={$skipped}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}