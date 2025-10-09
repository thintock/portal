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
