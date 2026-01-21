<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\FeedbackPost;

class MonthlyItemFeedbackPosts extends Component
{
    use WithPagination;

    public int $monthlyItemId;

    public string $q = '';
    public int $perPage = 20;

    // sort: newest / oldest
    public string $sort = 'newest';

    protected $paginationTheme = 'tailwind'; // daisyUI + Tailwind

    public function mount(int $monthlyItemId): void
    {
        $this->monthlyItemId = $monthlyItemId;
    }

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $q = trim($this->q);

        $posts = FeedbackPost::query()
          ->where('monthly_item_id', $this->monthlyItemId)
          ->with([
              'user:id,name',
          ])
          ->when($q !== '', function ($query) use ($q) {
              $like = '%' . addcslashes($q, '\\%_') . '%';
      
              $query->where(function ($sub) use ($like) {
                  $sub->where('title', 'like', $like)
                      ->orWhere('body', 'like', $like)
                      ->orWhereHas('user', function ($u) use ($like) {
                          $u->where('name', 'like', $like);
                      });
              });
          })
          ->when($this->sort === 'oldest', fn ($q) => $q->orderBy('created_at'))
          ->when($this->sort !== 'oldest', fn ($q) => $q->orderByDesc('created_at'))
          ->select([
              'id',
              'monthly_item_id',
              'user_id',
              'title',
              'body',
              'created_at',
          ])
          ->paginate($this->perPage);

        return view('livewire.admin.monthly-item-feedback-posts', [
            'posts' => $posts,
        ]);
    }
}
