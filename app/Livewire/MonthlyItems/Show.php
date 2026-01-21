<?php

namespace App\Livewire\MonthlyItems;

use Livewire\Component;
use App\Models\MonthlyItem;
use App\Models\FeedbackPost;

class Show extends Component
{
    public MonthlyItem $monthlyItem;

    public ?FeedbackPost $myPost = null;

    public bool $canCreate = false;
    public bool $canEdit = false;

    public function mount(MonthlyItem $monthlyItem)
    {
        $this->monthlyItem = $monthlyItem->load([
            'mediaFiles' => fn ($q) => $q->where('media_files.type', 'monthly_item_cover'),

            // 投稿は新しい順
            'feedbackPosts' => fn ($q) => $q->orderByDesc('created_at'),

            // 投稿者アバター
            'feedbackPosts.user.mediaFiles' => fn ($q) => $q->where('media_files.type', 'avatar'),

            // 投稿の添付画像
            'feedbackPosts.mediaFiles' => fn ($q) => $q->where('media_files.type', 'feedback_image'),
        ]);

        $userId = auth()->id();

        $this->myPost = $userId
            ? $this->monthlyItem->feedbackPosts->firstWhere('user_id', $userId)
            : null;

        $isOpen = $this->monthlyItem->isFeedbackOpen();

        $this->canCreate = $isOpen && !$this->myPost;
        $this->canEdit   = $isOpen && (bool) $this->myPost;
    }

    public function render()
    {
        return view('livewire.monthly-items.show')
            ->layout('layouts.app', ['title' => $this->monthlyItem->title ?? '月次テーマ']);
    }
}
