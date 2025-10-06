<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    /**
     * ルーム作成フォーム
     */
    public function create()
    {
        return view('admin.rooms.create');
    }

    /**
     * ルーム保存
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'description' => 'nullable|string|max:200',
            'visibility'  => 'required|in:public,members,private',
            'post_policy' => 'required|in:admins_only,members',
            'icon'        => 'nullable|image|max:5120',
            'cover_image' => 'nullable|image|max:5120',
        ]);
    
        // ファイル保存
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('rooms/icons', 'public');
        }
    
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('rooms/covers', 'public');
        }
    
        // 追加項目（サーバ側で決定）
        $data['slug']       = Str::slug($data['name']) . '-' . Str::random(6);
        $data['owner_id']   = auth()->id();
        $data['sort_order'] = (Room::max('sort_order') ?? 0) + 1;
        $data['is_active']  = false;
    
        Room::create($data);
    
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'ルームを作成しました（現在は非公開です）');
    }

    /**
     * ルーム編集フォーム
     */
    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * ルーム更新
     */
    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'description' => 'nullable|string|max:200',
            'visibility'  => 'required|in:public,members,private',
            'post_policy' => 'required|in:admins_only,members',
            'icon'        => 'nullable|image|max:5120',
            'cover_image' => 'nullable|image|max:5120',
            'is_active' => 'nullable|boolean',
        ]);
    
        // ファイル更新
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('rooms/icons', 'public');
        }
    
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('rooms/covers', 'public');
        }
        
        // チェックが外れていた場合はfalseにする
        $date['is_active'] = $request->boolean('is_active');
        
        $room->update($data);
    
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'ルームを更新しました');
    }

    /**
     * ルーム削除
     */
    public function destroy(Room $room)
    {
        // 運用中のルームは削除禁止
        if ($room->is_active) {
            return redirect()->back()
                ->with('error', 'このルームは運用中のため削除できません。運用を停止してから削除してください。');
        }
    
        // 投稿がある場合は削除禁止
        // if ($room->posts()->exists()) {
        //    return redirect()->back()
        //        ->with('error', 'このルームには投稿が存在するため削除できません。投稿を削除してから再度お試しください。');
        // }
    
        $room->delete();
    
        return redirect()->route('admin.dashboard')
            ->with('success', 'ルームを削除しました');
    }
}
