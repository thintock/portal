<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\Post;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // daisyUI + Breeze 対応

    public function render()
    {
        $user = Auth::user();

        // --- 有料会員のみデータ取得 ---
        $rooms = collect(); // デフォルト空コレクション
        $latestPosts = collect();

        // ルーム一覧（公開 + メンバー制 + 所属private）
        $rooms = Room::where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->whereIn('visibility', ['public', 'members'])
                      ->orWhere(function ($q) use ($user) {
                          $q->where('visibility', 'private')
                            ->whereHas('members', function ($sub) use ($user) {
                                $sub->where('user_id', $user->id);
                            });
                      });
            })
            ->withCount([
                'posts' => fn($q) => $q->whereNull('deleted_at'),
            ])
            ->with(['mediaFiles' => fn($q) => $q->whereIn('type', ['room_icon', 'room_cover'])])
            ->orderBy('sort_order')
            ->orderByDesc('last_posted_at')
            ->paginate(12);

        // 新着投稿（所属または公開ルームのみ）
        $latestPosts = Post::with(['user', 'room'])
            ->whereHas('room', function ($query) use ($user) {
                $query->where('visibility', 'public')
                      ->orWhere(function ($q) use ($user) {
                          $q->where('visibility', 'members')
                            ->whereHas('members', fn($sub) => $sub->where('user_id', $user->id));
                      })
                      ->orWhere(function ($q) use ($user) {
                          $q->where('visibility', 'private')
                            ->whereHas('members', fn($sub) => $sub->where('user_id', $user->id));
                      });
            })
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dashboard.index', [
            'user' => $user,
            'rooms' => $rooms,
            'latestPosts' => $latestPosts,
        ])->layout('layouts.app');
    }
}
