<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * イベント一覧（有料会員専用）
     */
    public function index()
    {
        // 公開・開催予定・開催中のイベントを取得
        $events = Event::whereIn('status', ['published', 'ongoing'])
            ->where('visibility', '!=', 'hidden')
            ->orderBy('start_at', 'asc')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * イベント詳細ページ
     */
    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // 閲覧制限（public/members/paid_members）
        if ($event->visibility === 'paid_members' && !Auth::user()?->subscribed()) {
            abort(403, 'このイベントは有料会員限定です。');
        }

        // 参加情報（ログイン中ユーザー）
        $participant = null;
        if (Auth::check()) {
            $participant = $event->participants()
                ->where('user_id', Auth::id())
                ->first();
        }

        // メディアファイル（画像・動画）
        $mediaFiles = $event->mediaFiles()
            ->whereHas('file', function ($q) {
                $q->whereIn('mime', ['image/jpeg', 'image/png', 'video/mp4']);
            })
            ->get();

        return view('events.show', [
            'event' => $event,
            'participant' => $participant,
            'mediaFiles' => $mediaFiles,
        ]);
    }
}
