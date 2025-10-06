<?php

namespace App\Livewire\Posts;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Post;

class PostEditModal extends Component
{
    use WithFileUploads;

    public $postId;
    public $body = '';
    public $media = [];     // 既存＋新規をまとめる
    public $newMedia = [];  // アップロード直後バッファ
    public $showModal = false;

    protected $listeners = ['open-post-edit' => 'open'];

    public function rules()
    {
        return [
            'body'      => 'required_without:media|string|max:5000',
            'media'     => 'array|max:10',
            'media.*'   => 'nullable', // ファイル or 既存パスが混在
            'newMedia'   => 'array',
            'newMedia.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
        ];
    }

    public function open($postId)
    {
        $this->postId = $postId;
        $post = Post::findOrFail($this->postId);

        $this->body = $post->body;
        $this->media = $post->media_json ?? []; // 既存パスを配列でセット
        $this->newMedia = [];
        $this->showModal = true;
    }

    public function updatedNewMedia()
    {
        if (!empty($this->newMedia)) {
            $this->validateOnly('newMedia.*');

            $total = count($this->media) + count($this->newMedia);
            if ($total > 10) {
                $this->addError('media', '最大10個までです。');
                $this->newMedia = [];
                return;
            }

            // 新規ファイルを末尾に追加
            $this->media = array_merge($this->media, $this->newMedia);
            $this->newMedia = [];
        }
    }

    public function removeMedia($index)
    {
        if (isset($this->media[$index])) {
            unset($this->media[$index]);
            $this->media = array_values($this->media);
        }
    }

    public function moveUp($index)
    {
        if ($index > 0) {
            [$this->media[$index - 1], $this->media[$index]] =
                [$this->media[$index], $this->media[$index - 1]];
        }
    }

    public function moveDown($index)
    {
        if ($index < count($this->media) - 1) {
            [$this->media[$index + 1], $this->media[$index]] =
                [$this->media[$index], $this->media[$index + 1]];
        }
    }

    public function save()
    {
        $this->validate();

        $mediaPaths = [];
        foreach ($this->media as $item) {
            if (is_string($item)) {
                $mediaPaths[] = $item;
            } elseif (is_object($item)) {
                $mediaPaths[] = $item->store('posts', 'public');
            }
        }

        $post = Post::findOrFail($this->postId);
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $post->update([
            'body'       => $this->body,
            'media_json' => $mediaPaths,
        ]);

        $this->dispatch('post-updated', postId: $this->postId);
        $this->reset(['newMedia']);
        $this->showModal = false;

        session()->flash('success', '投稿を編集しました');
    }

    public function render()
    {
        return view('livewire.posts.post-edit-modal');
    }
}
