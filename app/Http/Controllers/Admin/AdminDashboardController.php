<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Event;
use App\Models\Subscription;

class AdminDashboardController extends Controller
{
    /**
     * 管理者ダッシュボード
     */
    public function index()
    {
        $stats = [
            'users_count'          => User::count(),
            //'active_subscriptions' => Subscription::where('stripe_status', 'active')->count(),
            //'posts_count'          => Post::count(),
            //'events_count'         => Event::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
