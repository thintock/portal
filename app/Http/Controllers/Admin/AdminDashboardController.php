<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Event;
use Laravel\Cashier\Subscription;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 月次ユーザー数（直近6ヶ月）
        $userMonthlyCounts = User::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("COUNT(*) as count")
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // サイト統計
        $stats = [
            'users_count'          => User::count(),
            'active_subscriptions' => Subscription::where('stripe_status', 'active')->count(),
            'posts_count'          => Post::count(),
            'comments_count'       => Comment::count(),
        ];

        // 直近イベント5件
        $recentEvents = Event::orderBy('start_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'userMonthlyCounts', 'recentEvents'));
    }
}
