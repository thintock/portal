<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomMember;
use Illuminate\Http\Request;

class RoomMemberController extends Controller
{
    /**
     * ルームに参加
     */
    public function join(Room $room)
    {
        $member = RoomMember::firstOrCreate(
            ['room_id' => $room->id, 'user_id' => auth()->id()],
            ['role' => 'member', 'joined_at' => now()]
        );

        return back()->with('success', 'ルームに参加しました');
    }

    /**
     * ルームから退出
     */
    public function leave(Room $room)
    {
        RoomMember::where('room_id', $room->id)
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('success', 'ルームから退出しました');
    }

    /**
     * メンバーの役割変更（管理者権限想定）
     */
    public function updateRole(Request $request, RoomMember $member)
    {
        $this->authorize('updateRole', $member);

        $validated = $request->validate([
            'role' => 'required|in:owner,admin,moderator,member',
        ]);

        $member->update(['role' => $validated['role']]);

        return back()->with('success', 'メンバーの役割を更新しました');
    }
}
