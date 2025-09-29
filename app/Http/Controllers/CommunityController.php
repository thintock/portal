<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class CommunityController extends Controller
{
    /**
     * コミュニティトップページ
     * 有料会員のみアクセス可能（ルートで subscribed ミドルウェアを適用）
     */
    public function index()
    {
        $rooms = Room::where('is_active', true)
            ->withCount([
                'posts' => function ($query) {
                    $query->whereNull('deleted_at'); // 論理削除された投稿を除外
                }
            ])
            ->orderBy('sort_order')
            ->orderByDesc('last_posted_at')
            ->paginate(12);

        return view('community.index', compact('rooms'));
    }
}
