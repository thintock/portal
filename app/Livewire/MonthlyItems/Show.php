<?php

namespace App\Livewire\MonthlyItems;

use Livewire\Component;
use App\Models\MonthlyItem;
use App\Models\FeedbackPost;

class Show extends Component
{
    public MonthlyItem $monthlyItem;

    public ?MonthlyItem $prevItem = null;
    public ?MonthlyItem $nextItem = null;
    
    public ?FeedbackPost $myPost = null;

    public bool $canCreate = false;
    public bool $canEdit = false;

    public function mount(MonthlyItem $monthlyItem)
    {
        $this->monthlyItem = $monthlyItem->load([
            'mediaFiles' => fn ($q) => $q
            ->whereIn('media_files.type', ['monthly_item_cover', 'monthly_item_gallery'])
            ->orderBy('media_relations.sort_order'),

            // 投稿は新しい順
            'feedbackPosts' => fn ($q) => $q->orderByDesc('created_at'),

            // 投稿者アバター
            'feedbackPosts.user.mediaFiles' => fn ($q) => $q->where('media_files.type', 'avatar'),

            // 投稿の添付画像
            'feedbackPosts.mediaFiles' => fn ($q) => $q->where('media_files.type', 'feedback_image'),
        ]);
        
        // ✅ 前後の月次テーマ（存在する場合だけ）
        $this->prevItem = MonthlyItem::query()
            ->where('month', '<', $this->monthlyItem->month)
            ->where('status', 'published') // 公開中だけ出すなら
            ->orderByDesc('month')
            ->first();

        $this->nextItem = MonthlyItem::query()
            ->where('month', '>', $this->monthlyItem->month)
            ->where('status', 'published')
            ->orderBy('month')
            ->first();

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
