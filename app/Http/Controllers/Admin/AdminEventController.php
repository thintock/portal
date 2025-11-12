<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminEventController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        $events = Event::latest()->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    /**
     * 新規作成フォーム
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * 登録処理
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:events,slug',
            'event_type' => 'nullable|string|max:100',
            'body1' => 'nullable|string',
            'body2' => 'nullable|string',
            'body3' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'location' => 'nullable|string|max:255',
            'join_url' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'recept' => 'boolean',
            'status' => 'required|string|max:50',
            'visibility' => 'required|string|max:50',
            'cover_image' => 'nullable|image|max:10240',
            'gallery.*'   => 'nullable|image|max:10240',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']) . '-' . uniqid();
        $validated['user_id'] = Auth::id();

        $event = Event::create($validated);

        $disk = config('filesystems.default', 'public');

        // ✅ カバー画像
        if ($request->hasFile('cover_image')) {
            $media = MediaFile::uploadAndCreate(
                $request->file('cover_image'),
                $event,
                'event_cover',
                $disk,
                'events/covers'
            );

            MediaRelation::create([
                'mediable_type' => Event::class,
                'mediable_id'   => $event->id,
                'media_file_id' => $media->id,
                'sort_order'    => 0,
            ]);
        }

        // ✅ ギャラリー画像（複数）
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $i => $image) {
                $media = MediaFile::uploadAndCreate(
                    $image,
                    $event,
                    'event_gallery',
                    $disk,
                    'events/gallery'
                );

                MediaRelation::create([
                    'mediable_type' => Event::class,
                    'mediable_id'   => $event->id,
                    'media_file_id' => $media->id,
                    'sort_order'    => $i + 1,
                ]);
            }
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'イベントを作成しました。');
    }

    /**
     * 編集フォーム
     */
    public function edit(Event $event)
    {
        $cover   = $event->mediaFiles()->where('type', 'event_cover')->first();
        $gallery = $event->mediaFiles()->where('type', 'event_gallery')
                        ->orderBy('media_relations.sort_order')
                        ->get();
                        
        return view('admin.events.edit', compact('event', 'cover', 'gallery'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => "nullable|string|max:255|unique:events,slug,{$event->id}",
            'event_type' => 'nullable|string|max:100',
            'body1' => 'nullable|string',
            'body2' => 'nullable|string',
            'body3' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'location' => 'nullable|string|max:255',
            'join_url' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'recept' => 'boolean',
            'status' => 'required|string|max:50',
            'visibility' => 'required|string|max:50',
            'cover_image' => 'nullable|image|max:10240',
            'gallery.*'   => 'nullable|image|max:10240',
        ]);

        $event->update($validated);
        $disk = config('filesystems.default', 'public');

        // ✅ カバー更新（既存削除 → 新規登録）
        if ($request->hasFile('cover_image')) {
            $oldCover = $event->mediaFiles()->where('type', 'event_cover')->first();
            if ($oldCover) {
                $oldCover->delete();
            }

            $media = MediaFile::uploadAndCreate(
                $request->file('cover_image'),
                $event,
                'event_cover',
                $disk,
                'events/covers'
            );

            MediaRelation::create([
                'mediable_type' => Event::class,
                'mediable_id'   => $event->id,
                'media_file_id' => $media->id,
                'sort_order'    => 0,
            ]);
        }

        // ✅ ギャラリー追加（削除機能は別で）
        if ($request->hasFile('gallery')) {
            $currentCount = $event->mediaFiles()
                ->where('type', 'event_gallery')
                ->count();

            foreach ($request->file('gallery') as $i => $image) {
                $media = MediaFile::uploadAndCreate(
                    $image,
                    $event,
                    'event_gallery',
                    $disk,
                    'events/gallery'
                );

                MediaRelation::create([
                    'mediable_type' => Event::class,
                    'mediable_id'   => $event->id,
                    'media_file_id' => $media->id,
                    'sort_order'    => $currentCount + $i + 1,
                ]);
            }
        }

        return redirect()->route('admin.events.edit', $event)
            ->with('success', 'イベントを更新しました。');
    }

    /**
     * 削除処理
     */
    public function destroy(Event $event)
    {
        // ✅ 紐づくメディア削除
        $mediaFiles = $event->mediaFiles;
        foreach ($mediaFiles as $file) {
            $file->delete();
        }
        
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'イベントを削除しました。');
    }
}
