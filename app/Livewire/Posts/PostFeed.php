<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Models\Room;
use Livewire\Component;
use Livewire\WithPagination;

class PostFeed extends Component
{
    use WithPagination;

    public Room $room;
    public int $perPage = 3;

    protected $listeners = [
        'post-created' => 'refreshList',
        'post-deleted' => 'refreshList',
        'load-more-posts' => 'loadMore',
    ];

    public function mount(Room $room)
    {
        $this->room = $room;
    }

    public function refreshList()
    {
        // 最新に戻す
        $this->resetPage();
    }
    
    public function loadMore()
    {
        $this->perPage += 1;
    }
    
    public function render()
    {
        $posts = Post::where('room_id', $this->room->id)
            ->orderByDesc('last_activity_at')
            ->paginate($this->perPage);

        return view('livewire.posts.post-feed', compact('posts'));
    }
}
