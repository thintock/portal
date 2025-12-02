<?php

namespace App\Livewire\Comments;

use App\Models\Comment;
use App\Models\Post;
use App\Helpers\TextHelper;
use App\Models\Notification;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class CommentSection extends Component
{
    use WithFileUploads;

    public Post $post;

    public string $body = '';
    /** @var array<\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $media = [];
    public array $newMedia = []; // 選択直後のバッファ
    public ?int $replyTo = null;
    public int $perPage = 3;
    public int $formKey = 0;  // textareaリセット用
    public bool $showForm = false; // 新規コメント投稿フォームのトグル
    public array $repliesPerParent = []; // 親コメントごとの表示件数を管理

    protected $listeners = [
        'comment-created' => '$refresh',
        'comment-updated' => '$refresh',
        'deleteComment' => 'delete',
        'reply-created' => '$refresh',
        ];
        
    protected function rules(): array
    {
        return [
            'body'     => 'required_without:media|string|max:2000',
            'media.*'  => 'nullable|file|max:1048576|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
        ];
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
    }

    // newMedia更新時にバッファ検証 & mediaへマージ
    public function updatedNewMedia(): void
    {
        if (!empty($this->newMedia)) {
            $this->validate([
                'newMedia'   => 'array',
                'newMedia.*' => 'file|max:1048576|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
            ]);

            $total = count($this->media) + count($this->newMedia);
            if ($total > 3) {
                $this->addError('media', '一度に投稿できるファイルは最大3個までです。');
                $this->newMedia = [];
                return;
            }

            $this->media = array_values(array_merge($this->media, $this->newMedia));
            $this->newMedia = [];
        }
    }

    public function removeMedia($index): void
    {
        if (isset($this->media[$index])) {
            unset($this->media[$index]);
            $this->media = array_values($this->media);
        }
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

    public function save(?int $parentId = null): void
    {
        $this->validate();
    
        DB::transaction(function () use ($parentId) {
            // 親コメントを取得（返信の場合）
            $parent = $parentId ? Comment::find($parentId) : null;
    
            // コメント登録
            $comment = Comment::create([
                'post_id'   => $this->post->id,
                'user_id'   => Auth::id(),
                'parent_id' => $parent?->id,
                'root_id'   => $parent ? ($parent->root_id ?? $parent->id) : null,
                'body'      => $this->body,
                'status'    => 'published',
                'depth'     => $parent ? $parent->depth + 1 : 0,
            ]);
    
            // 添付ファイル登録（MediaFile::uploadAndCreate 統一版）
            if (!empty($this->media)) {
                $disk = config('filesystems.default');
    
                foreach ($this->media as $index => $file) {
                    // ファイルアップロード & MediaFile作成
                    $media = MediaFile::uploadAndCreate(
                        $file,
                        Auth::user(),
                        'comment',
                        $disk,
                        'comments/' . $comment->id
                    );
    
                    // MediaRelation登録（コメントとの紐付け）
                    MediaRelation::create([
                        'media_file_id' => $media->id,
                        'mediable_type' => Comment::class,
                        'mediable_id'   => $comment->id,
                        'sort_order'    => $index,
                    ]);
                }
            }
    
            // カウント更新
            if ($parent) {
                $parent->increment('replies_count');
            } else {
                $this->post->increment('comment_count');
            }
    
            // 通知作成
            $receiverId = null;
            $type = null;
            $message = null;
            $roomId = $this->post->room_id ?? null;
    
            // コメント本文を30文字で抜粋
            $commentExcerpt = mb_substr(strip_tags($comment->body), 0, 30);
            if (mb_strlen($comment->body) > 30) {
                $commentExcerpt .= '…';
            }
            
            // 投稿者とコメント投稿者が異なる場合のみ通知
            if ($this->post->user_id !== Auth::id()) {
                $receiverId = $this->post->user_id;
                $type = 'comment';
                $message = $commentExcerpt;
    
                if ($receiverId && $type && $message) {
                    Notification::create([
                        'user_id'         => $receiverId,
                        'sender_id'       => Auth::id(),
                        'notifiable_id'   => $comment->id,
                        'notifiable_type' => Comment::class,
                        'type'            => $type,
                        'message'         => $message,
                        'room_id'         => $roomId,
                    ]);
                }
            }
    
            // Postsのアクティビティを更新
            $this->post->update([
                'last_activity_at' => now(),
            ]);
        });
    
        // 初期化処理（トランザクション外）
        $this->reset(['body', 'media', 'replyTo']);
        $this->formKey++;
        $this->showForm = false;
    
        session()->flash('success', '投稿しました');
        session()->flash('success', $parentId ? '返信を投稿しました' : 'コメントを投稿しました');
    }

    public function setReplyTo($commentId): void
    {
        $this->replyTo = $this->replyTo === $commentId ? null : $commentId;
    }

    public function delete($commentId): void
    {
        $comment = Comment::where('post_id', $this->post->id)
            ->with('mediaFiles') // メディアも一緒に取得
            ->findOrFail($commentId);
    
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }
        
        // 通知削除（このコメントが notifiable として登録されている通知）
        Notification::where('notifiable_id', $comment->id)
            ->where('notifiable_type', Comment::class)
            ->delete();

        // 子コメント（返信）がある場合、その通知も削除（任意）
        Notification::whereIn(
            'notifiable_id',
            Comment::where('parent_id', $comment->id)->pluck('id')
        )->where('notifiable_type', Comment::class)->delete();
        
        // --- media_relations のみ削除（media_files は保持）---
        MediaRelation::where('mediable_id', $comment->id)
            ->where('mediable_type', Comment::class)
            ->delete();
        
        $comment->delete();
        
        // 親コメント or 投稿のコメント数調整
        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->decrement('replies_count');
        } else {
            $this->post->decrement('comment_count');
        }
        
        session()->flash('success', 'コメントを削除しました');
    }

    public function loadMore(): void
    {
        $this->perPage += 5;
    }
    
    public function loadMoreReplies($parentId)
    {
        $initial = 1;
        if (isset($this->repliesPerParent[$parentId])) {
            $this->repliesPerParent[$parentId] += 5;
        } else {
            $this->repliesPerParent[$parentId] = $initial + 5; // 初期1 + 5
        }
    }
    
    public function refresh()
    {
        // 最新に戻す
        $this->resetPage();
    }
    public function render()
    {
        $parents = Comment::where('post_id', $this->post->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate($this->perPage);

        // ✅ 各コメント本文をリンク化＆短縮化
        foreach ($parents as $parent) {
            $body = $parent->body ?? '';
            $parent->formatted_body = TextHelper::linkify($body);

            $short = mb_substr($body, 0, 100);
            if (mb_strlen($body) > 100) {
                $short .= '…';
            }
            $parent->short_body = TextHelper::linkify($short);

            foreach ($parent->replies as $reply) {
                $rbody = $reply->body ?? '';
                $reply->formatted_body = TextHelper::linkify($rbody);

                $rshort = mb_substr($rbody, 0, 100);
                if (mb_strlen($rbody) > 100) {
                    $rshort .= '…';
                }
                $reply->short_body = TextHelper::linkify($rshort);
            }
        }

        // 初期値設定
        foreach ($parents as $parent) {
            if (!isset($this->repliesPerParent[$parent->id])) {
                $this->repliesPerParent[$parent->id] = 1;
            }
        }

        return view('livewire.comments.comment-section', compact('parents'));
    }
}
