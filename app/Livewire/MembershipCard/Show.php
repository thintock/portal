<?php

namespace App\Livewire\MembershipCard;

use Livewire\Component;
use App\Models\User;

class Show extends Component
{
    public $user;
    public bool $showModal = false;

    /** 会員判定を統合 */
    public bool $isMember = false;

    /** 会員番号やロールでの表示制御を統合 */
    public ?string $memberStatus = null;

    protected $listeners = ['show-membership-card' => 'open'];

    /**
     * モーダル表示
     */
    public function open($userId)
    {
        $this->user = User::with([
            'mediaFiles' => function ($q) {
                $q->where('media_files.type', 'avatar')
                  ->orderBy('media_relations.sort_order', 'asc');
            }
        ])->find($userId);

        if (! $this->user) return;

        /** ▼ 会員判定ロジック（Blade で書いていた部分を統合） */
        $this->isMember = $this->determineMembership($this->user);
        $this->memberStatus = $this->determineStatusText($this->user);

        $this->showModal = true;
    }

    /**
     * モーダル閉じる
     */
    public function close()
    {
        $this->showModal = false;
    }

    /**
     * 会員かどうかの最終判定
     */
    private function determineMembership(User $user): bool
    {
        // 管理者・ゲストは常にフルカード
        if ($user->role === 'admin' || $user->role === 'guest') {
            return true;
        }

        // サブスクがある（Laravel Cashier）
        if ($user->subscribed('default')) {
            return true;
        }

        // 会員番号がある
        if (!empty($user->member_number)) {
            return true;
        }

        return false;
    }

    /**
     * 会員ステータス文言（カードに表示）
     */
    private function determineStatusText(User $user): string
    {
        if ($user->role === 'admin') {
            return 'Official';
        }
        if ($user->role === 'guest') {
            return 'Guest';
        }
        if ($user->member_number) {
            return 'No. ' . $user->member_number;
        }

        return 'Free Member'; // 会員以外
    }

    public function render()
    {
        return view('livewire.membership-card.show');
    }
}
