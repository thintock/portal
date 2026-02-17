<?php

namespace App\Livewire\SavedPosts;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\SavedPostCategory;

class SaveButton extends Component
{
    public Post $post;

    public bool $saved = false;

    public ?int $savedPostId = null;
    public ?int $selectedCategoryId = null;

    public string $newCategoryName = '';

    /** @var array<int, array{id:int, name:string}> */
    public array $categories = [];

    public bool $showModal = false;

    public function mount(Post $post): void
    {
        $this->post = $post;

        $this->refreshState();
        $this->loadCategories();
    }

    public function refreshState(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            $this->saved = false;
            $this->savedPostId = null;
            $this->selectedCategoryId = null;
            return;
        }

        $saved = SavedPost::query()
            ->where('user_id', $userId)
            ->where('post_id', $this->post->id)
            ->first();

        $this->saved = (bool) $saved;
        $this->savedPostId = $saved?->id;
        $this->selectedCategoryId = $saved?->saved_post_category_id;
    }

    public function loadCategories(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            $this->categories = [];
            return;
        }

        $this->categories = SavedPostCategory::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
            ->all();
    }

    public function openModal(): void
    {
        if (!Auth::check()) {
            return;
        }

        // 最新状態に合わせる
        $this->refreshState();
        $this->loadCategories();

        $this->newCategoryName = '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            return;
        }

        $this->newCategoryName = trim($this->newCategoryName);

        $this->validate([
            'selectedCategoryId' => ['nullable', 'integer'],
            'newCategoryName'    => ['nullable', 'string', 'max:50'],
        ]);

        $categoryId = $this->selectedCategoryId;

        // 新規カテゴリ名が入っていれば作る（同名は firstOrCreate で吸収）
        if ($this->newCategoryName !== '') {
            $category = SavedPostCategory::query()->firstOrCreate(
                [
                    'user_id' => $userId,
                    'name'    => $this->newCategoryName,
                ],
                [
                    'user_id' => $userId,
                    'name'    => $this->newCategoryName,
                ]
            );

            $categoryId = $category->id;
        }

        SavedPost::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'post_id' => $this->post->id,
            ],
            [
                'saved_post_category_id' => $categoryId,
            ]
        );

        $this->refreshState();
        $this->loadCategories();

        $this->showModal = false;

        // 必要ならトーストイベント等に使える
        $this->dispatch('saved-post-updated', postId: $this->post->id);
    }

    public function remove(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            return;
        }

        SavedPost::query()
            ->where('user_id', $userId)
            ->where('post_id', $this->post->id)
            ->delete();

        $this->refreshState();
        $this->showModal = false;

        $this->dispatch('saved-post-updated', postId: $this->post->id);
    }

    public function render()
    {
        return view('livewire.saved-posts.save-button');
    }
    
    public function deleteCategory(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            return;
        }
    
        // 未分類（null）は削除対象外
        if (!$this->selectedCategoryId) {
            return;
        }
    
        // 自分のカテゴリ以外は消せない（権限チェック）
        $category = SavedPostCategory::query()
            ->where('id', $this->selectedCategoryId)
            ->where('user_id', $userId)
            ->first();
    
        if (!$category) {
            return;
        }
    
        // 削除（saved_posts は FK の nullOnDelete により未分類へ）
        $category->delete();
    
        // UI状態更新
        $this->selectedCategoryId = null; // 未分類に戻す
        $this->newCategoryName = '';
    
        $this->loadCategories();
        $this->refreshState();
    
        $this->dispatch('saved-category-deleted', categoryId: (int) $category->id);
    }

}
