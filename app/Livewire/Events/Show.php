<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public Event $event;
    public $cover;
    public $gallery;

    public function mount($slug)
    {
        $user = Auth::user();

        $this->event = Event::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        // 公開制御
        if (!$this->event->isVisibleTo($user)) {
            abort(403, 'このイベントは閲覧できません。');
        }

        $this->cover = $this->event->mediaFiles()->where('type', 'event_cover')->first();
        $this->gallery = $this->event->mediaFiles()
            ->where('type', 'event_gallery')
            ->orderBy('media_relations.sort_order')
            ->get();
    }

    public function render()
    {
        return view('livewire.events.show')
            ->layout('layouts.app', ['title' => $this->event->title]);
    }
}
