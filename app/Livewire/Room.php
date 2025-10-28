<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Room as RoomModel;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Room extends Component
{
    use WithPagination;

    public RoomModel $room;

    protected $paginationTheme = 'tailwind';
    
    #[On('membership-changed')]
    public function refreshMembership($roomId)
    {
        if ($roomId == $this->room->id) {
            $this->room->refresh()->load(['members', 'mediaFiles']);
        }
    }
    
    public function mount(RoomModel $room)
    {
        // mediaFiles を事前ロード
        $this->room = $room->load(['members', 'mediaFiles' => function ($q) {
            $q->whereIn('type', ['room_icon', 'room_cover']);
        }]);
    }

    public function render()
    {
        // 🔹 ログインユーザーを取得（ゲスト対応）
        $user = auth()->user();
    
        $posts = $this->room->posts()
            ->whereNull('deleted_at')
            ->with('user')
            ->latest()
            ->paginate(10);
            
        view()->share('room', $this->room);    
        
        // 🔹 表示対象の他ルーム（公開＋メンバー制＋所属private）
        $otherRooms = \App\Models\Room::where('is_active', true)
            ->where('id', '!=', $this->room->id)
            ->where(function ($query) use ($user) {
                if ($user) {
                    $query
                        // 公開ルームは常に表示
                        ->where('visibility', 'public')
                        // メンバー制 or private は「所属している」場合のみ
                        ->orWhere(function ($q) use ($user) {
                            $q->whereIn('visibility', ['members', 'private'])
                              ->whereHas('members', function ($sub) use ($user) {
                                  $sub->where('user_id', $user->id);
                              });
                        });
                } else {
                    // ゲストは public のみ
                    $query->where('visibility', 'public');
                }
            })
            ->orderBy('sort_order')
            ->orderByDesc('last_posted_at')
            ->limit(6)
            ->get(['id', 'name', 'visibility']);
            
        return view('livewire.rooms.show', [
            'room' => $this->room,
            'posts' => $posts,
            'otherRooms' => $otherRooms,
        ])->layout('layouts.app',);
    }
}
