<?php

namespace App\Livewire\MembershipCard;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    public $user;
    public bool $showModal = false;

    protected $listeners = ['show-membership-card' => 'open'];

    /**
     * 会員証を開く
     */
    public function open($userId)
    {
        $this->user = User::with(['mediaFiles' => function ($q) {
            $q->where('media_files.type', 'avatar')
              ->orderBy('media_relations.sort_order', 'asc');
        }])->find($userId);

        if ($this->user) {
            $this->showModal = true;
        }
    }

    public function close()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.membership-card.show');
    }
}
