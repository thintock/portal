<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventParticipantController extends Controller
{
    /**
     * 参加申込処理
     */
    public function store(Request $request, Event $event)
    {
        if (!$event->recept) {
            return back()->with('error', 'このイベントは現在、参加を受け付けていません。');
        }

        if ($event->capacity && $event->participants()->where('status', 'going')->count() >= $event->capacity) {
            return back()->with('error', '定員に達しています。');
        }

        EventParticipant::updateOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => Auth::id(),
            ],
            [
                'status' => 'going',
                'comment' => $request->input('comment'),
            ]
        );

        return back()->with('success', 'イベントに参加登録しました。');
    }

    /**
     * キャンセル処理
     */
    public function destroy(Event $event)
    {
        $participant = EventParticipant::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($participant) {
            $participant->update(['status' => 'cancelled']);
            return back()->with('success', '参加をキャンセルしました。');
        }

        return back()->with('error', '参加情報が見つかりません。');
    }
}
