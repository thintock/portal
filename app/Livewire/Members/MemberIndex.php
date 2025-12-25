<?php

namespace App\Livewire\Members;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MemberIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';
    public int $perPage = 30;
    public string $sort = 'newest';
    public string $search = '';
    
    public function loadMore()
    {
        $this->perPage += 30;
    }
    
    protected $listeners = [
        'membercard-closed' => '$refresh',
    ];
    
    public function mount()
    {
        $user = auth()->user();
    
        // 管理者は許可
        if ($user->role === 'admin') {
            return;
        }
    
        // ゲストユーザーは不可
        if ($user->role === 'guest' || !$user->subscribed('default')) {
            abort(403, 'このページにアクセスする権限がありません。');
        }
    }

    /** 検索や並び替えが更新されたときページを戻す */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    /** ベースクエリ */
    protected function baseQuery()
    {
        return DB::table('users')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('subscriptions')
                  ->whereColumn('subscriptions.user_id', 'users.id')
                  ->where('subscriptions.type', 'default')
                  ->where('subscriptions.stripe_status', 'active');
            })
            ->leftJoin('member_number_histories', 'member_number_histories.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.role',
                DB::raw('NULL as joined_at'),
                'member_number_histories.number as member_no'
            );
    }



    /**
     * 検索 + 並べ替え
     */
    public function getMembersQuery()
    {
        $query = $this->baseQuery();

        // --- 検索 ---
        if ($this->search !== '') {
            $terms = collect(explode(' ', mb_convert_kana($this->search, 's')))
                ->filter()
                ->values();

            foreach ($terms as $term) {
                $query->where('name', 'like', "%{$term}%");
            }
        }
        
        // --- 並び替え ---
        $query = match ($this->sort) {
            'member_no' => $query->orderBy('member_no', 'asc'),
            'oldest'    => $query->orderBy('joined_at', 'asc'),
            'newest'    => $query->orderBy('joined_at', 'desc'),
            'name'      => $query->orderBy('name', 'asc'),
            default     => $query->orderBy('joined_at', 'desc'),
        };

        return $query;
    }


    /**
     * 描画直前で User モデルに復元
     */
    public function render()
    {
        $raw = $this->getMembersQuery()->paginate($this->perPage);

        // User モデルに復元
        $members = User::whereIn('id', $raw->pluck('id'))->get()
            ->keyBy('id');

        // ページネーション順に並べ替え
        $ordered = $raw->getCollection()->map(function ($row) use ($members) {
            $user = $members[$row->id];
            $user->joined_at = $row->joined_at;
            $user->member_no = $row->member_no;
            return $user;
        });

        // ページネーションオブジェクトに差し替え
        $raw->setCollection($ordered);

        return view('livewire.members.member-index', [
            'members' => $raw,
        ])->layout('layouts.app', [
            'title' => '会員一覧'
        ]);
    }
}