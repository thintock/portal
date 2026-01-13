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

    // 無限スクロール用
    public int $perPage = 20;
    public bool $hasMore = true;
    public bool $isLoading = false;

    protected $queryString = [
        'roomId' => ['except' => null],
        'q'      => ['except' => ''],
        // 'page' は無限スクロールだと体験が崩れやすいので外す
    ];

    public function updatingRoomId(): void
    {
        $this->resetInfinite();
    }

    public function updatingQ(): void
    {
        $this->resetInfinite();
    }

    private function resetInfinite(): void
    {
        $this->resetPage();
        $this->hasMore = true;
        $this->isLoading = false;
    }

    public function loadMore(): void
    {
        // 二重発火・終端を防ぐ
        if ($this->isLoading || !$this->hasMore) return;

        $this->isLoading = true;

        // WithPagination の現在ページを進める
        $this->setPage($this->getPage() + 1);

        $this->isLoading = false;
    }

    public function render()
    {
        $user = Auth::user();

        $query = Post::query()
            ->with(['user', 'room'])
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
            ->latest();

        $posts = $query->paginate($this->perPage);

        // 次ページがあるか
        $this->hasMore = $posts->hasMorePages();

        return view('livewire.posts.index', [
            'posts' => $posts,
        ])->layout('layouts.app');
    }
}
