<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * コメント保存
     */
    public function store(Request $request, Room $room,Post $post)
    {
        $validated = $request->validate([
            'body'      => 'nullable|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
            'media.*'   => 'nullable|file|max:10240', // 1ファイル10MBまで
        ]);
    
        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('comments', 'public');
                $mediaPaths[] = $path;
            }
        }
    
        // root_id を決定
        $rootId = null;
        $depth  = 0;
        if (!empty($validated['parent_id'])) {
            $parent = Comment::find($validated['parent_id']);
            $rootId = $parent->root_id ?? $parent->id;
            $depth  = ($parent->depth ?? 0) + 1;
        }
    
        Comment::create([
            'post_id'     => $post->id,
            'parent_id'   => $validated['parent_id'] ?? null,
            'root_id'     => $rootId,
            'user_id'     => Auth::id(),
            'body'        => $validated['body'] ?? null,
            'media_json'  => !empty($mediaPaths) ? $mediaPaths : null,
            'status'      => 'published',
            'depth'       => $depth,
        ]);
    
        return back()->with('success', 'コメントを投稿しました');
    }

    /**
     * 編集フォーム
     */
    public function edit(Room $room, Post $post, Comment $comment)
    {
        // 投稿者本人しか編集できない
        if ($comment->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        return view('comments.edit', compact('post', 'comment'));
    }

    /**
     * コメント更新
     */
    public function update(Request $request, Room $room,Post $post,  Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }
    
        $validated = $request->validate([
            'body'     => 'nullable|string|max:2000',
            'media.*'  => 'nullable|file|max:10240', // 添付ファイル（複数可）
        ]);
    
        $mediaPaths = $comment->media_json ?? [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('comments', 'public');
                $mediaPaths[] = $path;
            }
        }
    
        $comment->update([
            'body'       => $validated['body'] ?? $comment->body,
            'media_json' => !empty($mediaPaths) ? $mediaPaths : null,
        ]);
    
        return back()->with('success', 'コメントを更新しました');
    }

    /**
     * コメント削除
     */
    public function destroy(Room $room, Post $post, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        $comment->delete();

        return back()->with('success', 'コメントを削除しました');
    }
}
