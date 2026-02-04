<?php

namespace App\Livewire\SavedPosts;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedPost;
use App\Models\SavedPostCategory;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $q = '';
    public ?int $boxId = null; // 保存箱（= saved_post_category_id）
    public int $perPage = 20;
    public int $step = 20;

    protected $listeners = [
        'load-more-saved-posts' => 'loadMore',
        // 保存解除/変更が起きたら一覧をリフレッシュしたい場合に使える
        'saved-post-updated' => '$refresh',
    ];

    protected $queryString = [
        'q'     => ['except' => ''],
        'boxId' => ['except' => null],
    ];

    public function updatingQ(): void
    {
        $this->resetList();
    }

    public function updatingBoxId(): void
    {
        $this->resetList();
    }

    public function resetList(): void
    {
        $this->resetPage();
        $this->perPage = 20;
    }

    public function loadMore(): void
    {
        $this->perPage += $this->step;
    }

    public function render()
    {
        $user = Auth::user();

        // 保存箱一覧（左カラムやセレクトに使う）
        $boxes = $user->savedPostCategories()
            ->orderBy('name')
            ->get(['id', 'name']);

        // SavedPost 起点で取る（ベストプラクティス）
        $savedPosts = SavedPost::query()
            ->where('user_id', $user->id)
            ->when($this->boxId, fn($q) => $q->where('saved_post_category_id', $this->boxId))
            ->with([
                'post.user',
                'post.room',
                // サムネ用：postの画像だけ・順序は sort_order
                'post.mediaFiles' => fn ($q) => $q->where('media_files.type', 'post')
                    ->orderBy('media_relations.sort_order'),
                'category',
            ])
            // キーワード検索（post.body）
            ->when($this->q !== '', function ($q) {
                $keyword = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $this->q) . '%';
                $q->whereHas('post', fn($p) => $p->where('body', 'like', $keyword));
            })
            // 並び順：保存した順が使いやすい（必要なら post の新着順にも変更可）
            ->latest('created_at')
            // 無限スクロールに強い
            ->simplePaginate($this->perPage);

        return view('livewire.saved-posts.index', [
            'savedPosts' => $savedPosts,
            'boxes'      => $boxes,
        ])->layout('layouts.app');
    }
    
    public function deleteBox(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            return;
        }
    
        if (!$this->boxId) {
            return;
        }
    
        $box = SavedPostCategory::query()
            ->where('id', $this->boxId)
            ->where('user_id', $userId)
            ->first();
    
        if (!$box) {
            return;
        }
    
        $deletedId = $box->id;
        $box->delete(); // saved_posts.saved_post_category_id は nullOnDelete で未分類へ
    
        // フィルタ解除＆再読込
        $this->boxId = null;
        $this->resetList();
    
        $this->dispatch('saved-box-deleted', boxId: (int) $deletedId);
    }
}
