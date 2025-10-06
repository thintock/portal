<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Room as RoomModel;
use Livewire\WithPagination;

class Room extends Component
{
    use WithPagination;

    public RoomModel $room;

    protected $paginationTheme = 'tailwind';
    
    #[On('membership-changed')]
    public function refreshMembership($roomId)
    {
        if ($roomId == $this->room->id) {
            $this->room->refresh()->load('members');
        }
    }
    
    public function mount(RoomModel $room)
    {
        $this->room = $room->load('members');
    }

    public function render()
    {
        // ルームの投稿一覧（コメントなどは post-card 内で処理）
        $posts = $this->room->posts()
            ->whereNull('deleted_at')
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.rooms.show', [
            'room' => $this->room,
            'posts' => $posts,
        ])->layout('layouts.app');
    }
}
