<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostCard extends Component
{
    public Post $post;
    protected $listeners = ['post-updated' => 'refreshPost'];
    
    public function delete()
    {
        if ($this->post->user_id !== Auth::id()) {
            abort(403);
        }
        $room = $this->post->room;
        $this->post->delete();

        // 集計を安全に更新（最低0）
        $room->decrement('posts_count');
        if ($room->posts_count < 0) {
            $room->update(['posts_count' => 0]);
        }

        $this->dispatch('post-deleted');
        session()->flash('success', '投稿を削除しました');
        return redirect()->route('rooms.show', $room);
    }

    public function refreshPost($postId)
    {
        if ($this->post->id == $postId) {
            $this->post->refresh(); // モデルをリロード
        }
    }
    
    public function render()
    {
        return view('livewire.posts.post-card');
    }
}
