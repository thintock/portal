<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminAnnouncementController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        $announcements = Announcement::query()
        ->orderByDesc('created_at') // 作成日の新しい順
        ->paginate(30);

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * 新規作成フォーム
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', 'unique:announcements,slug'],
            'body'             => ['nullable', 'string'], // HTML可なら string でOK
            'visibility'       => ['required', 'in:public,membership,admin'],
            'publish_start_at' => ['nullable', 'date'],
            'publish_end_at'   => ['nullable', 'date', 'after:publish_start_at'],
        ]);

        // slug 未入力なら自動生成（必ずユニークに寄せる）
        $validated['slug'] = $validated['slug'] ?: $this->generateUniqueSlug($validated['title']);

        $validated['user_id'] = Auth::id();

        $announcement = Announcement::create($validated);

        return redirect()
            ->route('admin.announcements.edit', $announcement)
            ->with('success', 'お知らせを作成しました。');
    }
    
    public function show(Announcement $announcement)
    {
        // カバー（単数）
        $cover = $announcement->mediaFiles()
            ->where('type', 'announcement_cover')
            ->orderBy('media_relations.sort_order')
            ->first();
    
        // ギャラリー（複数）
        $gallery = $announcement->mediaFiles()
            ->where('type', 'announcement_gallery')
            ->orderBy('media_relations.sort_order')
            ->get();
    
        return view('admin.announcements.show', compact('announcement', 'cover', 'gallery'));
    }
    
    /**
     * 編集フォーム
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * 更新
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:255', 'unique:announcements,slug,' . $announcement->id],
            'body'             => ['nullable', 'string'],
            'visibility'       => ['required', 'in:public,membership,admin'],
            'publish_start_at' => ['nullable', 'date'],
            'publish_end_at'   => ['nullable', 'date', 'after:publish_start_at'],
        ]);

        $announcement->update($validated);

        return redirect()
            ->route('admin.announcements.edit', $announcement)
            ->with('success', 'お知らせを更新しました。');
    }

    /**
     * 削除
     */
    public function destroy(Announcement $announcement)
    {
        // MediaRelation を使っている場合、必要なら関連を削除してから本体削除
        // 例）$announcement->mediaRelations()->delete();
        // ただし MediaFile 自体を消すかは運用次第（共有してる可能性があるなら消さない）

        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'お知らせを削除しました。');
    }

    /**
     * タイトルからユニークな slug を生成
     */
    private function generateUniqueSlug(string $title): string
    {
        // 日本語だと Str::slug が空になりやすいのでフォールバックを入れる
        $base = Str::slug($title);

        if ($base === '') {
            $base = 'announcement';
        }

        $slug = $base;
        $i = 1;

        while (Announcement::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
