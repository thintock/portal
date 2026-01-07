<?php

namespace App\Livewire\Dashboard\Partials;

use Livewire\Component;
use App\Models\Announcement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Announcements extends Component
{
    public int $limit = 5;

    public function render()
    {
        $user = auth()->user();

        // visibleTo の中で visibility を見ている前提ですが、
        // 念のためダッシュボード側は public/membership のみに絞る（現状維持）
        $announcements = Announcement::query()
            ->visibleTo($user)
            ->whereIn('visibility', ['public', 'membership'])
            ->latest()
            ->take($this->limit)
            // mediaFiles（pivot=media_relations）の sort_order も必要なので eager load
            ->with([
                'mediaFiles' => function ($q) {
                    $q->where('type', 'announcement_cover')
                      ->orderBy('media_relations.sort_order');
                }
            ])
            ->get()
            ->map(function ($a) {
                // 代表画像：カバーを1枚（無ければnull）
                $img = $a->mediaFiles->first();

                $a->cover_url = $img
                    ? ($img->url ?? Storage::url($img->path))
                    : null;

                return $a;
            });

        return view('livewire.dashboard.partials.announcements', [
            'announcements' => $announcements,
        ]);
    }
}
