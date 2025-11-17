<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ✅ config から取得
        $requiredSlugs = config('pages.required');
    
        // 🔍 一般ページ（必須スラッグ以外）
        $pages = Page::with(['creator', 'updater'])
            ->whereNotIn('slug', array_keys($requiredSlugs))
            ->orderBy('id', 'asc')
            ->paginate(10);
    
        // 🔍 必須ページの存在確認
        $requiredPages = collect($requiredSlugs)->mapWithKeys(function ($label, $slug) {
            return [$slug => Page::where('slug', $slug)->first()];
        });
    
        return view('admin.pages.index', compact('pages', 'requiredPages', 'requiredSlugs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $presetSlug  = $request->query('slug');
        $requiredSlugs = config('pages.required');
        $presetTitle = $requiredSlugs[$presetSlug] ?? '';
    
        return view('admin.pages.create', compact('presetSlug', 'presetTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'slug' => 'required|string|unique:pages,slug|max:255',
            'body1' => 'nullable|string',
            'body2' => 'nullable|string',
            'body3' => 'nullable|string',
        ]);
        
        $validated['created_by'] = auth()->id();
        $validated['status'] = "draft"; 
        
        Page::create($validated);

        return redirect()->route('admin.pages.index')->with('success', 'ページを作成しました。');
    }

    public function show(Page $page)
    {
        $page->load(['creator', 'updater']);

        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'body1' => 'nullable|string',
            'body2' => 'nullable|string',
            'body3' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);
        
        $validated['updated_by'] = auth()->id();

        $page->update($validated);

        return redirect()->route('admin.pages.edit', $page->id)->with('success', 'ページを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
    
        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'ページを削除しました。');
    }
}
