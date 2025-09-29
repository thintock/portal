<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * 投稿一覧
     */
    public function index(Room $room)
    {
        $posts = Post::where('room_id', $room->id)
            ->orderByDesc('pinned_at')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('posts.index', compact('room', 'posts'));
    }

    /**
     * 新規投稿フォーム
     */
    public function create(Room $room)
    {
        return view('posts.create', compact('room'));
    }

    /**
     * 投稿保存
     */
    public function store(Request $request, Room $room)
    {
        $validated = $request->validate([
            'body'         => 'required|string|max:5000',
            'post_type'    => 'in:post,event',
            'visibility'   => 'in:public,members,private',
            'external_url' => 'nullable|url|max:500',
            'media.*'      => 'nullable|file|max:102400|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi', 
        ]);
        // 本文は HTML をそのまま保存せず、文字列として扱う
        // → Blade 側表示で e() を使う
        $body = $request->input('body');
    
        // 複数ファイルの保存処理
        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public'); // storage/app/public/posts
                $mediaPaths[] = $path;
            }
        }
    
        // 投稿作成
        Post::create([
            'room_id'       => $room->id,
            'user_id'       => Auth::id(),
            'post_type'     => $request->input('post_type', 'post'),
            'body'          => $body,
            'visibility'    => $request->input('visibility', 'public'),
            'external_url'  => $request->input('external_url'),
            'media_json'    => !empty($mediaPaths) ? $mediaPaths : null,
        ]);
    
        return redirect()
            ->route('rooms.show', $room)
            ->with('success', '投稿を作成しました');
    }

    /**
     * 投稿詳細（必要なら）
     */
    public function show(Room $room, Post $post)
    {
        return view('posts.show', compact('room', 'post'));
    }

    /**
     * 投稿編集フォーム
     */
    public function edit(Room $room, Post $post)
    {
        // 投稿者本人のみ編集可
        if ($post->user_id !== auth()->id()) {
            abort(403, 'この投稿を編集する権限がありません');
        }
    
        return view('posts.edit', compact('room', 'post'));
    }

    /**
     * 投稿更新
     */
    public function update(Request $request, Room $room, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        $validated = $request->validate([
            'body'         => 'required|string|max:5000',
            'visibility'   => 'in:public,members,private',
            'external_url' => 'nullable|url|max:500',
            'media.*'      => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm',
        ]);

        // 本文
        $post->body = $validated['body'];
        $post->visibility = $validated['visibility'] ?? $post->visibility;
        $post->external_url = $validated['external_url'] ?? null;

        // 画像・動画の追加
        if ($request->hasFile('media')) {
            $mediaPaths = $post->media_json ?? [];
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');
                $mediaPaths[] = $path;
            }
            $post->media_json = $mediaPaths;
        }

        $post->save();

        return redirect()->route('rooms.show', $room)->with('success', '投稿を更新しました');
    }

    /**
     * 投稿削除
     */
    public function destroy(Room $room, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }
    
        $post->delete();
    
        return redirect()->route('rooms.show', $room)->with('success', '投稿を削除しました');
    }
}
