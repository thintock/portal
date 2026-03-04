<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use App\Services\PointService;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $user = User::find($comment->user_id);
        if (! $user) return;

        // ✅ reply(=parent_idあり) でも comment.created に統一
        $actionType = 'comment.created';

        // ✅ room_id は comments には無いので post から取る（earn の points 解決に必要）
        $roomId = Post::whereKey($comment->post_id)->value('room_id');
        $roomId = $roomId ? (int) $roomId : null;

        app(PointService::class)->earn(
            user: $user,
            actionType: $actionType,
            subject: $comment,
            roomId: $roomId
        );
    }

    public function deleted(Comment $comment): void
    {
        $userId = $comment->user_id ?? $comment->getOriginal('user_id');
        $user = $userId ? User::find($userId) : null;
        if (! $user) return;

        // ✅ reply でも comment.deleted に統一
        $actionType = 'comment.created';

        // ✅ revoke は roomId 不要（PointService::revoke の引数に無いので渡さない）
        app(PointService::class)->revoke(
            user: $user,
            actionType: $actionType,
            subject: $comment
        );
    }

    public function forceDeleted(Comment $comment): void
    {
        $this->deleted($comment);
    }
}