<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\Auth;

class RsvpButton extends Component
{
    public Event $event;
    public bool $isJoined = false;
    public bool $isFull = false;

    public function mount(Event $event)
    {
        $this->isJoined = $event->is_joined;
        $this->isFull   = $event->is_full;
    }

    /** 参加/取り消しを切り替える */
    public function toggle()
    {
        $user = auth()->user();
        if (!$user) {
            session()->flash('message', 'ログインが必要です。');
            return;
        }
    
        if (!$this->isJoined && $this->event->is_full) {
            session()->flash('message', '定員に達しています。');
            return;
        }
    
        $participant = \App\Models\EventParticipant::where('event_id', $this->event->id)
            ->where('user_id', $user->id)
            ->first();
    
        if ($participant && $participant->status === 'going') {
            $participant->delete();
            $this->isJoined = false;
        } else {
            \App\Models\EventParticipant::updateOrCreate(
                ['event_id' => $this->event->id, 'user_id' => $user->id],
                ['status' => 'going']
            );
            $this->isJoined = true;
        }
    
        // 最新状態に更新
        $this->isFull = $this->event->fresh()->is_full;
    
        $this->dispatch('rsvpUpdated');
    }


    public function render()
    {
        return view('livewire.events.rsvp-button');
    }
}
