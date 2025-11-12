<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationModal extends Component
{
    public bool $showModal = false;
    public string $filter = 'all'; // all | comment | reaction

    protected $listeners = [
        'open-notifications' => 'open',
        'notification-created' => '$refresh',
    ];

    public function open(): void
    {
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
    }

    /**
     * 通知リスト（フィルタ対応）
     */
    public function getNotificationsProperty()
    {
        $query = Notification::with('sender')
            ->where('user_id', Auth::id())
            ->whereNotNull('notifiable_type')
            ->latest()
            ->take(30);

        if ($this->filter === 'comment') {
            $query->whereIn('type', ['comment', 'reply']);
        } elseif ($this->filter === 'reaction') {
            $query->where('type', 'reaction');
        }

        return $query->get()->map(fn ($n) => $this->formatNotification($n));
    }

    /**
     * 通知を整形
     */
    protected function formatNotification($n)
    {
        $map = [
            'comment'  => ['icon' => '💬', 'title' => 'コメントが届きました'],
            'reply'    => ['icon' => '↩️', 'title' => '返信が届きました'],
            'reaction' => ['icon' => '❤️', 'title' => 'リアクションがありました'],
            'message'  => ['icon' => '✉️', 'title' => 'メッセージが届きました'],
        ];
        $meta = $map[$n->type] ?? ['icon' => '🔔', 'title' => 'お知らせ'];

        $avatar = optional($n->sender)
            ->mediaFiles()
            ->where('media_files.type', 'avatar')
            ->first();

        return [
            'id'        => $n->id,
            'icon'      => $meta['icon'],
            'title'     => $meta['title'],
            'sender'    => $n->sender?->name ?? 'ユーザー名未登録',
            'avatar'    => $avatar?->path,
            'message'   => $n->message ? Str::limit(strip_tags($n->message), 100) : null,
            'read_at'   => $n->read_at,
            'created_at'=> $n->created_at?->diffForHumans(),
            'type'      => $n->type,
            'notifiable_type' => $n->notifiable_type,
            'notifiable_id'   => $n->notifiable_id,
        ];
    }

    /**
     * 現在のフィルタに応じて既読対象を変える
     */
    public function markAllAsRead(): void
    {
        $query = Notification::where('user_id', Auth::id())
            ->whereNull('read_at');

        if ($this->filter === 'comment') {
            $query->whereIn('type', ['comment', 'reply']);
        } elseif ($this->filter === 'reaction') {
            $query->where('type', 'reaction');
        }

        $count = $query->count();
        if ($count > 0) {
            $query->update(['read_at' => now()]);
            session()->flash('success', "{$count}件の通知を既読にしました。");
        } else {
            session()->flash('info', '未読の通知はありません。');
        }
    }

    /**
     * 通知個別クリック時の処理
     */
    public function markAsReadAndRedirect($id): void
    {
        $notification = Notification::where('user_id', Auth::id())->find($id);
        if (!$notification) return;

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        $url = route('dashboard');

        if ($notification->notifiable_type === \App\Models\Comment::class) {
            $comment = \App\Models\Comment::find($notification->notifiable_id);
            if ($comment) {
                $url = route('posts.show', ['post' => $comment->post_id]);
            }
        } elseif ($notification->notifiable_type === \App\Models\Reaction::class) {
            $reaction = \App\Models\Reaction::find($notification->notifiable_id);
            if ($reaction) {
                if ($reaction->reactionable_type === \App\Models\Post::class) {
                    $url = route('posts.show', ['post' => $reaction->reactionable_id]);
                } elseif ($reaction->reactionable_type === \App\Models\Comment::class) {
                    $comment = \App\Models\Comment::find($reaction->reactionable_id);
                    if ($comment) {
                        $url = route('posts.show', ['post' => $comment->post_id]);
                    }
                }
            }
        }

        $this->dispatch('redirect', url: $url);
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.notifications.notification-modal');
    }
}
