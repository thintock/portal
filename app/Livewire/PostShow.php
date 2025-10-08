<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class PostShow extends Component
{
    public Post $post;

    public function mount(Post $post)
    {
        // 投稿＋関連データを読み込み
        $this->post = $post->load(['user', 'room', 'comments.user']);
    }

    public function render()
    {
        return view('livewire.post-show', [
            'post' => $this->post,
        ])->layout('layouts.app');
    }
}
