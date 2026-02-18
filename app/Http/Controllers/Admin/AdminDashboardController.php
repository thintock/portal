<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Reaction;
use Laravel\Cashier\Subscription;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $months = $this->monthsRange(6); // 直近6ヶ月（今月含む）

        // 月次新規ユーザー数
        $userMonthlyCounts = $this->monthlyCount(User::query(), 'created_at', 6);

        // 月次 投稿数
        $postMonthlyCounts = $this->monthlyCount(Post::query(), 'created_at', 6);

        // 月次 コメント数
        $commentMonthlyCounts = $this->monthlyCount(Comment::query(), 'created_at', 6);

        // 月次 いいね数（Postへのlikeのみ）
        $likeMonthlyCounts = Reaction::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("COUNT(*) as count")
            )
            ->where('created_at', '>=', now()->startOfMonth()->subMonths(5))
            ->where('reactionable_type', Post::class)
            ->where('type', 'like') // ← ここはあなたのlike定義に合わせて（例: 'like'）
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $likeMonthlyCounts = $this->fillMonths($months, $likeMonthlyCounts);

        // 月次 契約中ユーザー数（その月にアクティブだった distinct user_id）
        $subscriptionMonthlyCounts = [];
        foreach ($months as $m) {
            $start = \Carbon\Carbon::createFromFormat('Y-m', $m)->startOfMonth();
            $end   = (clone $start)->endOfMonth();

            $subscriptionMonthlyCounts[$m] = Subscription::query()
                ->whereIn('stripe_status', ['active', 'trialing']) // 必要なら active のみに
                ->where('created_at', '<=', $end)
                ->where(function ($q) use ($start) {
                    $q->whereNull('ends_at')
                      ->orWhere('ends_at', '>=', $start);
                })
                ->distinct('user_id')
                ->count('user_id');
        }

        // サイト統計（既存）
        $stats = [
            'users_count'          => User::count(),
            'active_subscriptions' => Subscription::where('stripe_status', 'active')->count(),
            'posts_count'          => Post::count(),
            'comments_count'       => Comment::count(),
        ];

        // 直近イベント、管理者向けお知らせ（既存のまま）
        $recentEvents = \App\Models\Event::orderBy('start_at', 'desc')->take(5)->get();

        $now = now();
        $adminAnnouncements = \App\Models\Announcement::query()
            ->where('visibility', 'admin')
            ->where(function ($q) use ($now) {
                $q->whereNull('publish_start_at')->orWhere('publish_start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('publish_end_at')->orWhere('publish_end_at', '>=', $now);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'userMonthlyCounts',
            'subscriptionMonthlyCounts',
            'postMonthlyCounts',
            'commentMonthlyCounts',
            'likeMonthlyCounts',
            'recentEvents',
            'adminAnnouncements'
        ));
    }

    private function monthlyCount($query, string $column, int $monthsBack): array
    {
        $months = $this->monthsRange($monthsBack);

        $raw = $query->select(
                DB::raw("DATE_FORMAT($column, '%Y-%m') as month"),
                DB::raw("COUNT(*) as count")
            )
            ->where($column, '>=', now()->startOfMonth()->subMonths($monthsBack - 1))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return $this->fillMonths($months, $raw);
    }

    private function monthsRange(int $monthsBack): array
    {
        // 今月含めて monthsBack ヶ月分（例: 6 -> 今月〜5ヶ月前）
        $months = [];
        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $months[] = now()->copy()->subMonths($i)->format('Y-m');
        }
        return $months;
    }

    private function fillMonths(array $months, array $counts): array
    {
        $filled = [];
        foreach ($months as $m) {
            $filled[$m] = (int)($counts[$m] ?? 0);
        }
        return $filled;
    }
}
