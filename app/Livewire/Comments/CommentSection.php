<?php

namespace App\Livewire\Comments;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
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
        ];
        
    protected function rules(): array
    {
        return [
            'body'     => 'required_without:media|string|max:2000',
            'media.*'  => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
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
                'newMedia.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
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

        $paths = [];
        foreach ($this->media as $file) {
            $paths[] = $file->store('comments', 'public');
        }

        // 親コメントを取得（返信の場合）
        $parent = $parentId ? Comment::find($parentId) : null;

        $comment = Comment::create([
            'post_id'    => $this->post->id,
            'user_id'    => Auth::id(),
            'parent_id'  => $parent?->id,
            'root_id'    => $parent ? ($parent->root_id ?? $parent->id) : null,
            'body'       => $this->body,
            'media_json' => !empty($paths) ? $paths : null,
            'status'     => 'published',
            'depth'      => $parent ? $parent->depth + 1 : 0,
        ]);

        // カウント更新
        if ($parent) {
            $parent->increment('replies_count');
        } else {
            $this->post->increment('comment_count');
        }
        
        // 通知作成
        $actor = Auth::user();
        $receiverId = null;
        $type = null;
        $message = null;
        $roomId = $this->post->room_id ?? null;
        $commentExcerpt = mb_substr(strip_tags($comment->body), 0, 30);
        if (mb_strlen($comment->body) > 30) {
            $commentExcerpt .= '…';
        }
        
        if ($parent) {
            // 返信 → 親コメント投稿者に通知
            if ($parent->user_id !== $actor->id) {
                $receiverId = $parent->user_id;
                $type = 'reply';
                $message = "{$actor->display_name}さんからコメント「{$commentExcerpt}」";
            }
        } else {
            // 新規コメント → 投稿者に通知
            if ($this->post->user_id !== $actor->id) {
                $receiverId = $this->post->user_id;
                $type = 'comment';
                $message = "{$actor->display_name}さんからコメント「{$commentExcerpt}」";
            }
        }

        if ($receiverId && $type && $message) {
            Notification::create([
                'user_id'         => $receiverId,
                'notifiable_id'   => $comment->id,
                'notifiable_type' => Comment::class,
                'type'            => $type,
                'message'         => $message,
                'room_id'         => $roomId,
            ]);
        }
        
        // Postsのアクティビティを更新
        $this->post->update([
            'last_activity_at' => now(),
        ]);
        
        // 初期化
        $this->reset(['body', 'media', 'replyTo']);
        $this->formKey++;
        $this->showForm = false;

        session()->flash('success', $parent ? '返信を投稿しました' : 'コメントを投稿しました');
    }

    public function setReplyTo($commentId): void
    {
        $this->replyTo = $this->replyTo === $commentId ? null : $commentId;
    }

    public function delete($commentId): void
    {
        $comment = Comment::where('post_id', $this->post->id)->findOrFail($commentId);
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
        
        $comment->delete();
        $this->post->decrement('comment_count');
        session()->flash('success', 'コメントを削除しました');
    }

    public function loadMore(): void
    {
        $this->perPage += 5;
    }
    
    public function loadMoreReplies($parentId)
    {
        if (isset($this->repliesPerParent[$parentId])) {
            $this->repliesPerParent[$parentId] += 5;
        } else {
            $this->repliesPerParent[$parentId] = 6; // 初期1 + 5
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
        
        // 初期値設定：まだ存在しないキーには1をセット
        foreach ($parents as $parent) {
            if (!isset($this->repliesPerParent[$parent->id])) {
                $this->repliesPerParent[$parent->id] = 1;
            }
        }

        return view('livewire.comments.comment-section', compact('parents'));
    }
}
