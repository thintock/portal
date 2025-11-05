<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index()
    {
        
        $rooms = Room::orderBy('sort_order', 'asc')->get();

        return view('admin.rooms.index', compact('rooms'));
    }
    
    public function create()
    {
        return view('admin.rooms.create');
    }

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

        DB::transaction(function () use ($request, $data) {
            $room = new Room();
            $room->name        = $data['name'];
            $room->description = $data['description'] ?? null;
            $room->visibility  = $data['visibility'];
            $room->post_policy = $data['post_policy'];
            $room->slug        = Str::slug($data['name']) . '-' . Str::random(6);
            $room->owner_id    = auth()->id();
            $room->sort_order  = (Room::max('sort_order') ?? 0) + 1;
            $room->is_active   = false;
            $room->save();

            $disk = config('filesystems.default');

            // ✅ アイコン画像
            if ($request->hasFile('icon')) {
                $media = MediaFile::uploadAndCreate(
                    $request->file('icon'),
                    $room,
                    'room_icon',
                    $disk,
                    'rooms/icons'
                );
                MediaRelation::create([
                    'mediable_type' => Room::class,
                    'mediable_id'   => $room->id,
                    'media_file_id' => $media->id,
                    'sort_order'    => 0,
                ]);
            }

            // ✅ カバー画像
            if ($request->hasFile('cover_image')) {
                $media = MediaFile::uploadAndCreate(
                    $request->file('cover_image'),
                    $room,
                    'room_cover',
                    $disk,
                    'rooms/covers'
                );
                MediaRelation::create([
                    'mediable_type' => Room::class,
                    'mediable_id'   => $room->id,
                    'media_file_id' => $media->id,
                    'sort_order'    => 1,
                ]);
            }
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'ルームを作成しました（現在は非公開です）');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'description' => 'nullable|string|max:200',
            'visibility'  => 'required|in:public,members,private',
            'post_policy' => 'required|in:admins_only,members',
            'icon'        => 'nullable|image|max:5120',
            'cover_image' => 'nullable|image|max:5120',
            'is_active'   => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $room, $data) {
            $room->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'visibility'  => $data['visibility'],
                'post_policy' => $data['post_policy'],
                'is_active'   => $request->boolean('is_active'),
            ]);

            $disk = config('filesystems.default');

            // ✅ アイコン更新
            if ($request->hasFile('icon')) {
                // 古いアイコンrelationを削除
                MediaRelation::where('mediable_type', Room::class)
                    ->where('mediable_id', $room->id)
                    ->whereIn('media_file_id', function ($q) {
                        $q->select('id')->from('media_files')->where('type', 'room_icon');
                    })
                    ->delete();

                $media = MediaFile::uploadAndCreate(
                    $request->file('icon'),
                    $room,
                    'room_icon',
                    $disk,
                    'rooms/icons'
                );
                MediaRelation::create([
                    'mediable_type' => Room::class,
                    'mediable_id'   => $room->id,
                    'media_file_id' => $media->id,
                    'sort_order'    => 0,
                ]);
            }

            // ✅ カバー更新
            if ($request->hasFile('cover_image')) {
                MediaRelation::where('mediable_type', Room::class)
                    ->where('mediable_id', $room->id)
                    ->whereIn('media_file_id', function ($q) {
                        $q->select('id')->from('media_files')->where('type', 'room_cover');
                    })
                    ->delete();

                $media = MediaFile::uploadAndCreate(
                    $request->file('cover_image'),
                    $room,
                    'room_cover',
                    $disk,
                    'rooms/covers'
                );
                MediaRelation::create([
                    'mediable_type' => Room::class,
                    'mediable_id'   => $room->id,
                    'media_file_id' => $media->id,
                    'sort_order'    => 1,
                ]);
            }
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'ルームを更新しました');
    }

    public function destroy(Room $room)
    {
        if ($room->is_active) {
            return redirect()->back()
                ->with('error', 'このルームは運用中のため削除できません。');
        }

        $room->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'ルームを削除しました');
    }
}
