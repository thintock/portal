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

    /** ベースクエリ */
    protected function baseQuery()
    {
        // user_id ごとに「最新の default subscription」を1件だけ取得
        $latestSubscriptions = DB::table('subscriptions as s1')
            ->select(
                's1.user_id',
                's1.stripe_status',
                's1.created_at'
            )
            ->where('s1.type', 'default')
            ->whereRaw('s1.id = (
                SELECT MAX(s2.id)
                FROM subscriptions s2
                WHERE s2.user_id = s1.user_id
                  AND s2.type = "default"
            )');
    
        return DB::table('users')
            ->leftJoinSub($latestSubscriptions, 'subscriptions', function ($q) {
                $q->on('subscriptions.user_id', '=', 'users.id');
            })
            ->where(function ($q) {
                $q
                    // admin / guest は常に含める
                    ->whereIn('users.role', ['admin', 'guest'])
    
                    // それ以外は active subscription のみ
                    ->orWhere(function ($q2) {
                        $q2->where('subscriptions.stripe_status', 'active');
                    });
            })
            ->select(
                'users.id',
                'users.name',
                'users.role',
                'users.member_number as member_no',
                'subscriptions.created_at as joined_at'
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
        
        $query->orderBy('joined_at', 'desc');

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