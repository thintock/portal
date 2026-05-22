<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SwapSubscriptionsToBasicPrice extends Command
{
    /**
     * Examples:
     *
     * Dry run:
     * php artisan subscriptions:swap-basic-price --dry-run --old_price=price_xxx
     *
     * Run for a single user:
     * php artisan subscriptions:swap-basic-price --old_price=price_xxx --user_id=41
     *
     * Run for all target users:
     * php artisan subscriptions:swap-basic-price --old_price=price_xxx
     */
    protected $signature = 'subscriptions:swap-basic-price
        {--dry-run : Show target subscriptions without making changes}
        {--user_id= : Run only for a specific user ID}
        {--old_price= : Override the old Stripe Price ID}
        {--new_price= : Override the new Stripe Price ID}
    ';

    protected $description = 'Swap active subscriptions from the old Stripe Price to the new Stripe Price without proration';

    public function handle(): int
    {
        // Use STRIPE_PRICE_BASIC from .env as the default new Price ID.
        // The old Price ID must be explicitly provided using --old_price.
        $defaultNewPriceId = env('STRIPE_PRICE_BASIC');
        $defaultOldPriceId = null;

        $oldPriceId = (string) ($this->option('old_price') ?: $defaultOldPriceId);
        $newPriceId = (string) ($this->option('new_price') ?: $defaultNewPriceId);

        if ($oldPriceId === '' || $newPriceId === '') {
            $this->error('old_price or new_price is empty. Please specify --old_price=price_xxx.');
            return self::FAILURE;
        }

        if ($oldPriceId === $newPriceId) {
            $this->error('old_price and new_price are the same.');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $userId = $this->option('user_id');

        $this->info('Old Price: ' . $oldPriceId);
        $this->info('New Price: ' . $newPriceId);
        $this->info($dryRun ? 'Mode: dry-run (no changes will be made)' : 'Mode: execute');

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
        $this->info('Target user count: ' . $users->count());

        if ($users->isEmpty()) {
            $this->warn('No target users found.');
            return self::SUCCESS;
        }

        foreach ($users as $user) {
            // Use the already eager-loaded subscription relation.
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
            $this->info('Dry-run mode: no changes were made.');
            return self::SUCCESS;
        }

        if (! $this->confirm("Swap {$users->count()} subscription(s) to the new Price. Continue?")) {
            $this->warn('Operation cancelled.');
            return self::SUCCESS;
        }

        $success = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($users as $user) {
            try {
                // Use the already eager-loaded subscription relation.
                $subscription = $user->subscriptions->first();

                if (! $subscription) {
                    $skipped++;
                    $this->warn("skip user_id={$user->id}: subscription not found");
                    continue;
                }

                if ($subscription->stripe_price !== $oldPriceId) {
                    $skipped++;
                    $this->warn("skip user_id={$user->id}: current price is not the old price ({$subscription->stripe_price})");
                    continue;
                }

                if (! in_array($subscription->stripe_status, ['active', 'trialing'], true)) {
                    $skipped++;
                    $this->warn("skip user_id={$user->id}: status={$subscription->stripe_status}");
                    continue;
                }

                $oldPriceBeforeSwap = $subscription->stripe_price;
                $stripeSubscriptionId = $subscription->stripe_id;

                // Swap Price without proration.
                $subscription->noProrate()->swap($newPriceId);

                // Refresh the local subscription model.
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
        $this->info("Completed: success={$success} failed={$failed} skipped={$skipped}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}