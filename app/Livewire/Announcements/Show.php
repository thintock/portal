<?php

namespace App\Livewire\Announcements;

use Livewire\Component;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class Show extends Component
{
    public string $slug;

    public Announcement $announcement;

    public $cover = null;      // MediaFile|null
    public Collection $gallery; // ✅ 常にCollection

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->gallery = collect(); // ✅ 初期化（null事故防止）

        $user = Auth::user();

        $this->announcement = Announcement::query()
            ->visibleTo($user)
            ->where('slug', $this->slug)
            ->firstOrFail();

        $this->loadImages();
    }

    private function loadImages(): void
    {
        $this->cover = $this->announcement->mediaFiles()
            ->where('type', 'announcement_cover')
            ->orderBy('media_relations.sort_order')
            ->first();

        $this->gallery = $this->announcement->mediaFiles()
            ->where('type', 'announcement_gallery')
            ->orderBy('media_relations.sort_order')
            ->get();
    }

    public function render()
    {
        return view('livewire.announcements.show')
            ->layout('layouts.app'); // ✅ これが必須（MissingLayoutException回避）
    }
}
