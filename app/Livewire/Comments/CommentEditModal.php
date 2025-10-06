<?php

namespace App\Livewire\Comments;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentEditModal extends Component
{
    use WithFileUploads;

    public $commentId;
    public $body = '';
    public $media = [];     // 既存＋新規をまとめる
    public $newMedia = [];  // アップロード直後バッファ
    public int $formKey = 0;  // textareaリセット用
    public $showModal = false;

    protected $listeners = ['open-comment-edit' => 'open'];

    public function rules()
    {
        return [
            'body'      => 'required_without:media|string|max:2000',
            'media'     => 'array|max:5',
            'media.*'   => 'nullable', // ファイル or 既存パス
            'newMedia'   => 'array',
            'newMedia.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
        ];
    }

    public function open($commentId)
    {
        $this->commentId = $commentId;
        $comment = Comment::findOrFail($this->commentId);

        $this->body = $comment->body;
        $this->media = $comment->media_json ?? []; // 既存パスを配列でセット
        $this->newMedia = [];
        $this->showModal = true;
    }

    public function updatedNewMedia()
    {
        if (!empty($this->newMedia)) {
            $this->validateOnly('newMedia.*');

            $total = count($this->media) + count($this->newMedia);
            if ($total > 3) {
                $this->addError('media', '最大3個までです。');
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
                $mediaPaths[] = $item; // 既存のパス
            } elseif (is_object($item)) {
                $mediaPaths[] = $item->store('comments', 'public'); // 新規
            }
        }

        $comment = Comment::findOrFail($this->commentId);
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->update([
            'body'       => $this->body,
            'media_json' => $mediaPaths,
        ]);

        $this->reset(['newMedia']);
        $this->formKey++;
        $this->showModal = false;
        $this->dispatch('comment-updated', commentId: $this->commentId);

        session()->flash('success', 'コメントを編集しました');
    }

    public function render()
    {
        return view('livewire.comments.comment-edit-modal');
    }
}
