<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Room;
use App\Models\Post;

class Community extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // Laravel Breeze/DaisyUIならこれでOK

    public function render()
    {
        $rooms = Room::where('is_active', true)
            ->where(function ($query) {
                $query->whereIn('visibility', ['public', 'members']) // 公開＋メンバー制は常に表示
                      ->orWhere(function ($q) { // private は参加している場合のみ
                          $q->where('visibility', 'private')
                            ->whereHas('members', function ($sub) {
                                $sub->where('user_id', auth()->id());
                            });
                      });
            })
            ->withCount([
                'posts' => fn($q) => $q->whereNull('deleted_at'),
            ])
            ->orderBy('sort_order')
            ->orderByDesc('last_posted_at')
            ->paginate(12);
        
        $latestPosts = Post::with('user', 'room')
            ->whereHas('room', function ($query) {
                $query->where('visibility', 'public')
                      ->orWhere(function ($q) { // members で参加している場合のみ
                          $q->where('visibility', 'members')
                            ->whereHas('members', function ($sub) {
                                $sub->where('user_id', auth()->id());
                            });
                      })
                      ->orWhere(function ($q) { // private で参加している場合のみ
                          $q->where('visibility', 'private')
                            ->whereHas('members', function ($sub) {
                                $sub->where('user_id', auth()->id());
                            });
                      });
            })
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.communities.index', [
            'rooms' => $rooms,
            'latestPosts' =>$latestPosts,
        ])->layout('layouts.app');
    }
}
