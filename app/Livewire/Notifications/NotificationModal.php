<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationModal extends Component
{
    public bool $showModal = false;
    public $notifications = [];

    protected $listeners = [
        'open-notifications' => 'open',
        'notification-created' => '$refresh', // 通知作成時に更新
    ];

    public function open(): void
    {
        $this->showModal = true;
        $this->loadNotifications();
    }

    public function close(): void
    {
        $this->showModal = false;
    }

    public function loadNotifications(): void
    {
        $this->notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->take(30)
            ->get();
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $this->loadNotifications();
    }

    public function markAsReadAndRedirect($id): void
    {
        $notification = Notification::where('user_id', Auth::id())->find($id);
        if (!$notification) return;

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        // デフォルトはコミュニティトップ
        $url = route('dashboard');
        
        // モーダルを閉じる
        $this->showModal = false;
        
        // 通知タイプに応じて投稿IDを特定
        if ($notification->notifiable_type === \App\Models\Comment::class) {
            $comment = \App\Models\Comment::find($notification->notifiable_id);
            if ($comment) {
                $url = route('posts.show', ['post' => $comment->post_id]);
            }
        } 
        elseif ($notification->notifiable_type === \App\Models\Reaction::class) {
            $reaction = \App\Models\Reaction::find($notification->notifiable_id);
            if ($reaction) {
                // Reactionはpostまたはcommentのどちらにも属する可能性がある
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
    
        // JSでリダイレクト
        $this->dispatch('redirect', url: $url);
    }
    
    public function render()
    {
        return view('livewire.notifications.notification-modal');
    }
}
