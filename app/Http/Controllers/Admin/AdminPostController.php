<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $roomId = $request->query('room_id');

        $posts = Post::query()
            ->with([
                'user:id,name',
                'room:id,name',
            ])
            ->when($roomId, fn ($query) => $query->where('room_id', $roomId))
            ->when($q !== '', function ($query) use ($q) {
                $keyword = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('body', 'like', $keyword)
                        ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', $keyword))
                        ->orWhereHas('room', fn ($q2) => $q2->where('name', 'like', $keyword));
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.posts.index', compact('posts', 'q', 'roomId'));
    }

    public function destroy(Post $post)
    {
        $room = $post->room;

        $post->delete();

        if ($room) {
            $room->decrement('posts_count');
            if ($room->posts_count < 0) {
                $room->update(['posts_count' => 0]);
            }
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', '投稿を削除しました。');
    }
}