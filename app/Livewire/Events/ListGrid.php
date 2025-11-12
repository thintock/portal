<?php

namespace App\Livewire\Events;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Event;

class ListGrid extends Component
{
    use WithPagination;

    public string $tab = 'upcoming';
    public int $perPage = 6;
    public ?int $excludeId = null; // ← Sectionから受け取るID

    protected $updatesQueryString = ['tab'];

    public function render()
    {
        $user = auth()->user();

        $query = Event::query()
            ->visibleTo($user);

        if ($this->tab === 'past') {
            $query->past()->orderByDesc('end_at');
        } else {
            $query->upcoming()->orderBy('start_at');

            // ✅ Sectionで表示済みのイベントを除外
            if ($this->excludeId) {
                $query->where('id', '<>', $this->excludeId);
            }
        }

        $events = $query->withCount(['participants', 'activeParticipants'])
                        ->simplePaginate($this->perPage);

        return view('livewire.events.list-grid', compact('events'));
    }
}
