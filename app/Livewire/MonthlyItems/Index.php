<?php

namespace App\Livewire\MonthlyItems;

use Livewire\Component;
use App\Models\MonthlyItem;

class Index extends Component
{
    public function render()
    {
        $items = MonthlyItem::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with([
                'mediaFiles' => fn ($q) => $q
                    ->where('media_files.type', 'monthly_item_cover')
                    ->orderBy('media_relations.sort_order'),
            ])
            // 新しい順（公開日が新しいものが上）
            ->orderByDesc('published_at')
            // 同一 published_at の場合の並びを安定化
            ->orderByDesc('id')
            ->get();

        return view('livewire.monthly-items.index', compact('items'))
            ->layout('layouts.app', ['title' => '月次テーマ']);
    }
}
