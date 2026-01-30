<?php

namespace App\Livewire\Posts;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public ?int $roomId = null;
    public string $q = '';

    public int $perPage = 20;
    public int $step = 20;

    protected $listeners = [
        'load-more-posts' => 'loadMore',
    ];

    protected $queryString = [
        'roomId' => ['except' => null],
        'q'      => ['except' => ''],
    ];

    public function updatingRoomId(): void
    {
        $this->resetList();
    }

    public function updatingQ(): void
    {
        $this->resetList();
    }

    public function resetList(): void
    {
        $this->resetPage();
        $this->perPage = 20;
    }

    public function loadMore(): void
    {
        $this->perPage += $this->step;
    }

    public function render()
    {
        $user = Auth::user();

        $posts = Post::query()
            ->with([
                'user',
                'room',
                // ✅ サムネ生成のため（post の最新一覧にも同じロジックを使う）
                'mediaFiles' => fn ($q) => $q->where('media_files.type', 'post')
                    ->orderBy('media_relations.sort_order'),
            ])
            ->whereHas('room', function ($query) use ($user) {
                $query->where('visibility', 'public')
                    ->orWhere(function ($q) use ($user) {
                        $q->where('visibility', 'members')
                          ->whereHas('members', fn($sub) => $sub->where('user_id', $user->id));
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('visibility', 'private')
                          ->whereHas('members', fn($sub) => $sub->where('user_id', $user->id));
                    });
            })
            ->when($this->roomId, fn($q) => $q->where('room_id', $this->roomId))
            ->when($this->q !== '', function ($q) {
                $keyword = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $this->q) . '%';
                $q->where('body', 'like', $keyword);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.posts.index', [
            'posts' => $posts,
        ])->layout('layouts.app');
    }
}
