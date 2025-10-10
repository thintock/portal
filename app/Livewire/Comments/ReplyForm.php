<?php

namespace App\Livewire\Comments;

use App\Models\Comment;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReplyForm extends Component
{
    use WithFileUploads;

    public Comment $parent;
    public string $body = '';
    public array $media = [];
    public array $newMedia = [];
    public int $formKey = 0;
    public bool $isOpen = false;

    protected function rules(): array
    {
        return [
            'body' => 'required_without:media|string|max:2000',
            'media.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
        ];
    }

    public function updatedNewMedia(): void
    {
        if ($this->newMedia) {
            $this->validateOnly('newMedia.*');
            $this->media = array_merge($this->media, $this->newMedia);
            $this->newMedia = [];
        }
    }

    public function removeMedia($index): void
    {
        unset($this->media[$index]);
        $this->media = array_values($this->media);
    }

    public function moveUp($index): void
    {
        if ($index > 0) {
            [$this->media[$index - 1], $this->media[$index]] = [$this->media[$index], $this->media[$index - 1]];
        }
    }

    public function moveDown($index): void
    {
        if ($index < count($this->media) - 1) {
            [$this->media[$index + 1], $this->media[$index]] = [$this->media[$index], $this->media[$index + 1]];
        }
    }
    
    public function toggleForm(): void
    {
      $this->isOpen = !$this->isOpen;
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            $comment = Comment::create([
                'post_id'   => $this->parent->post_id,
                'user_id'   => Auth::id(),
                'parent_id' => $this->parent->id,
                'root_id'   => $this->parent->root_id ?? $this->parent->id,
                'body'      => $this->body,
                'depth'     => $this->parent->depth + 1,
                'status'    => 'published',
            ]);
            // ===============================
            // 🔹 メディアのアップロード処理
            // ===============================
            $disk = config('filesystems.default');
            foreach ($this->media as $i => $file) {
                $media = MediaFile::uploadAndCreate(
                    $file,
                    Auth::user(),
                    'comment',
                    $disk,
                    'comments/' . $comment->id
                );

                MediaRelation::create([
                    'media_file_id' => $media->id,
                    'mediable_type' => Comment::class,
                    'mediable_id'   => $comment->id,
                    'sort_order'    => $i,
                ]);
            }

            $this->parent->increment('replies_count');
            // ===============================
            // 🔔 通知作成処理
            // ===============================
            if ($this->parent->user_id !== Auth::id()) {
                // 本文の冒頭30文字を抜粋
                $commentExcerpt = mb_substr(strip_tags($comment->body), 0, 30);
                if (mb_strlen($comment->body) > 30) {
                    $commentExcerpt .= '…';
                }
                \App\Models\Notification::create([
                    'user_id'         => $this->parent->user_id, // 通知の受信者
                    'sender_id'       => Auth::id(),             // 通知の送信者（返信した人）
                    'notifiable_id'   => $comment->id,
                    'notifiable_type' => Comment::class,
                    'type'            => 'reply',
                    'message'         => $commentExcerpt,
                    'room_id'         => $this->parent->post->room_id ?? null,
                ]);
            }
        });

        $this->reset(['body', 'media', 'newMedia']);
        $this->formKey++;
        $this->dispatch('reply-created');
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.comments.reply-form');
    }
}
