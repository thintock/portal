<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminCommentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $postId = $request->query('post_id');

        $comments = Comment::query()
            ->with([
                'user:id,name',
                'post:id,body,room_id',
                'post.room:id,name',
                'parent:id,body',
            ])
            ->when($postId, fn ($query) => $query->where('post_id', $postId))
            ->when($q !== '', function ($query) use ($q) {
                $keyword = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('body', 'like', $keyword)
                        ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', $keyword))
                        ->orWhereHas('post', fn ($q2) => $q2->where('body', 'like', $keyword));
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.comments.index', compact('comments', 'q', 'postId'));
    }

    public function destroy(Comment $comment)
    {
        $post = $comment->post;
        
        // このコメント自身の通知を削除
        Notification::where('notifiable_id', $comment->id)
            ->where('notifiable_type', Comment::class)
            ->delete();

        // このコメントを親にもつ返信コメントの通知を削除
        Notification::whereIn(
            'notifiable_id',
            Comment::where('parent_id', $comment->id)->pluck('id')
        )->where('notifiable_type', Comment::class)->delete();
        
        $comment->delete();

        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->decrement('replies_count');
        } elseif ($post) {
            $post->decrement('comment_count');
            if ($post->comment_count < 0) {
                $post->update(['comment_count' => 0]);
            }
        }
        
        return redirect()
            ->route('admin.comments.index')
            ->with('success', 'コメントを削除しました。');
    }
}