<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;

class Section extends Component
{
    public string $tab = 'upcoming'; // upcoming|past
    public int $perPage = 6;

    // 👇 RSVP更新時のイベントをリッスンして再描画
    protected $listeners = ['rsvpUpdated' => '$refresh'];

    public function render()
    {
        $user = auth()->user();

        $next = Event::query()
            ->visibleTo($user)
            ->upcoming()
            ->orderBy('start_at')
            ->withCount(['participants', 'activeParticipants'])
            ->first();

        return view('livewire.events.section', [
            'next' => $next,
        ]);
    }
}
