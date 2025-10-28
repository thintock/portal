<?php

namespace App\Livewire\Comments;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Models\MediaFile;
use App\Models\MediaRelation;

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
            'newMedia.*' => 'file|max:1048576|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
        ];
    }

    public function open($commentId)
    {
        // 🔹 リレーションをロード
        $comment = Comment::with(['mediaFiles' => function ($q) {
            $q->orderBy('media_relations.sort_order');
        }])->findOrFail($commentId);
    
        $this->commentId = $comment->id;
        $this->body = $comment->body;
    
        // 🔹 MediaFile を配列に変換（Bladeで統一して扱いやすく）
        $this->media = $comment->mediaFiles->map(function ($file) {
            return [
                'id'   => $file->id,
                'path' => $file->path,
                'mime' => $file->mime,
            ];
        })->toArray();
    
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

        $comment = Comment::findOrFail($this->commentId);
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        DB::transaction(function () use ($comment) {
            // 1️⃣ 本文更新
            $comment->update(['body' => $this->body]);

            // 2️⃣ 既存のMediaRelationを削除（MediaFile自体は残す）
            MediaRelation::where('mediable_type', Comment::class)
                ->where('mediable_id', $comment->id)
                ->delete();

            $disk = config('filesystems.default');

            // 3️⃣ 新しいメディアを登録または再リンク
            foreach ($this->media as $index => $item) {
                if (is_array($item) && isset($item['id'])) {
                    // ✅ 既存MediaFileを再リンク
                    MediaRelation::create([
                        'mediable_type' => Comment::class,
                        'mediable_id'   => $comment->id,
                        'media_file_id' => $item['id'],
                        'sort_order'    => $index,
                    ]);
                } elseif (is_object($item)) {
                    // ✅ 新規アップロード
                    $media = MediaFile::uploadAndCreate(
                        $item,
                        Auth::user(),
                        'comment',
                        $disk,
                        'comments/' . $comment->id
                    );

                    MediaRelation::create([
                        'mediable_type' => Comment::class,
                        'mediable_id'   => $comment->id,
                        'media_file_id' => $media->id,
                        'sort_order'    => $index,
                    ]);
                }
            }

            // 4️⃣ 更新日時更新
            $comment->update(['updated_at' => now()]);
        });

        // 5️⃣ リセットと通知
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
