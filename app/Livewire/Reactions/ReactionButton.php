<?php

namespace App\Livewire\Reactions;

use Livewire\Component;
use App\Models\Reaction;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ReactionButton extends Component
{
    /** @var \Illuminate\Database\Eloquent\Model $model PostまたはCommentのインスタンス */
    public $model;

    /** 現在のユーザーがLIKEしているか */
    public bool $liked = false;

    /** LIKE数カウント */
    public int $likeCount = 0;

    protected $listeners = [
        'comment-created' => '$refresh',
        'comment-updated' => '$refresh',
        'comment-deleted' => '$refresh',
    ];

    /**
     * 初期化
     */
    public function mount($model)
    {
        $this->model = $model;
        $this->updateState();
    }

    /**
     * 現在のLIKE状態とカウントを更新
     */
    private function updateState(): void
    {
        $userId = Auth::id();

        // 現在ユーザーがLIKE済みかどうかを判定
        $this->liked = Reaction::where('reactionable_id', $this->model->id)
            ->where('reactionable_type', get_class($this->model))
            ->where('user_id', $userId)
            ->where('type', 'like')
            ->exists();

        // 総LIKE数をカウント
        $this->likeCount = Reaction::where('reactionable_id', $this->model->id)
            ->where('reactionable_type', get_class($this->model))
            ->where('type', 'like')
            ->count();
    }

    /**
     * LIKE のトグル処理（押すと追加、もう一度押すと削除）
     */
    public function toggleLike(): void
    {
        $user = Auth::user();

        // すでにLIKEしているか確認
        $existing = Reaction::where('reactionable_id', $this->model->id)
            ->where('reactionable_type', get_class($this->model))
            ->where('user_id', $user->id)
            ->where('type', 'like')
            ->first();

        if ($existing) {
            // すでにLIKEしている → 解除
            Notification::where('notifiable_id', $existing->id)
                ->where('notifiable_type', Reaction::class)
                ->where('type', 'like')
                ->delete();
                
            $existing->delete();
        } else {
            // LIKEを新規作成
            $reaction = Reaction::create([
                'user_id'           => $user->id,
                'reactionable_id'   => $this->model->id,
                'reactionable_type' => get_class($this->model),
                'type'              => 'like',
            ]);

            /**
             * 🔔 通知作成処理
             */
            $targetUserId = null;
            $type = 'like';
            $message = null;
            $roomId = null;
            $excerpt = '';

            // 投稿またはコメントの所有者を特定
            if (method_exists($this->model, 'user')) {
                $targetUserId = $this->model->user_id ?? null;
            }
            
            // room_idの取得（Postの場合は直接、Commentの場合はPost経由）
            if (isset($this->model->room_id)) {
                $roomId = $this->model->room_id;
            } elseif (method_exists($this->model, 'post') && $this->model->post) {
                $roomId = $this->model->post->room_id ?? null;
            }
            
            // 本文の一部抜粋（30文字）
            $excerpt = mb_substr(strip_tags($this->model->body), 0, 30);
            if (mb_strlen($this->model->body) > 30) {
                $excerpt .= '…';
            }
            
            // 自分自身には通知を送らない
            if ($targetUserId && $targetUserId !== $user->id) {
                $modelName = class_basename($this->model);

                // 通知メッセージをモデルに応じて分岐
                $message = match ($modelName) {
                    'Post' => "{$user->display_name}さんがあなたの投稿「{$excerpt}」に「❤️」しました。",
                    'Comment' => "{$user->display_name}さんがあなたのコメント「{$excerpt}」に「❤️️」しました。",
                    default => "{$user->display_name}さんがリアクションしました。",
                };

                // 通知レコードを作成
                Notification::create([
                    'user_id'         => $targetUserId,
                    'notifiable_id'   => $reaction->id,
                    'notifiable_type' => Reaction::class,
                    'type'            => $type,
                    'message'         => $message,
                    'room_id'         => $roomId,
                ]);
            }
        }

        // LIKE状態を再計算
        $this->updateState();
    }

    /**
     * コンポーネント描画
     */
    public function render()
    {
        return view('livewire.reactions.reaction-button');
    }
}
