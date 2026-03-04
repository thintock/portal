<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\User;
use App\Services\PointService;

class PostObserver
{
    public function created(Post $post): void
    {
        // user / room を最小コストで取得（relationsに依存しない）
        $user = User::find($post->user_id);
        if (! $user) return;

        app(PointService::class)->earn(
            user: $user,
            actionType: 'post.created',
            subject: $post,
            roomId: (int) $post->room_id,
        );
    }

    public function deleted(Post $post): void
    {
        // SoftDeleteでもここに来る
        $userId = $post->user_id ?? $post->getOriginal('user_id');
        $user = $userId ? User::find($userId) : null;
        if (! $user) return;

        app(PointService::class)->revoke(
            user: $user,
            actionType: 'post.created',
            subject: $post
        );
    }

    public function forceDeleted(Post $post): void
    {
        // 念のため（forceDeleteが将来入ってもOK）
        $this->deleted($post);
    }
}