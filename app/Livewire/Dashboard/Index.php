<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\Post;
use App\Models\Page;
use App\Models\MonthlyItem;
use App\Models\FeedbackPost;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // daisyUI + Breeze 対応

    public function render()
    {
        $user = Auth::user();

        // --- 固定ページを取得 ---
        $page = Page::where('slug', 'dashboard')->first();

        // --- サブスク初月判定 ---
        $showPaidIntroBanners = false;

        if ($user && $user->subscribed('default')) {
            $subscription = $user->subscription('default');

            if ($subscription && $subscription->created_at) {
                $showPaidIntroBanners = $subscription->created_at->diffInDays(now()) < 30;
            }
        }

        // --- ルーム/投稿 ---
        $rooms = collect();
        $latestPosts = collect();

        // ルーム一覧（公開 + メンバー制 + 所属private）
        $rooms = Room::where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->whereIn('visibility', ['public', 'members'])
                    ->orWhere(function ($q) use ($user) {
                        $q->where('visibility', 'private')
                            ->whereHas('members', function ($sub) use ($user) {
                                $sub->where('user_id', $user->id);
                            });
                    });
            })
            ->withCount([
                'posts' => fn ($q) => $q->whereNull('deleted_at'),
            ])
            ->with(['mediaFiles' => fn ($q) => $q->whereIn('type', ['room_icon', 'room_cover'])])
            ->orderBy('sort_order')
            ->orderByDesc('last_posted_at')
            ->paginate(12);

        // 新着投稿（所属または公開ルームのみ）
        $latestPosts = Post::with([
            'user',
            'room',
            'mediaFiles' => fn ($q) => $q->where('type', 'post')->orderBy('media_relations.sort_order'),
        ])
        ->whereHas('room', function ($query) use ($user) {
            $query->where('visibility', 'public')
                ->orWhere(function ($q) use ($user) {
                    $q->where('visibility', 'members')
                        ->whereHas('members', fn ($sub) => $sub->where('user_id', $user->id));
                })
                ->orWhere(function ($q) use ($user) {
                    $q->where('visibility', 'private')
                        ->whereHas('members', fn ($sub) => $sub->where('user_id', $user->id));
                });
        })
        ->latest()
        ->take(7)
        ->get();

        /**
         * ==============================
         * 月次テーマ（今月のテーマ：常に1件表示）
         * ==============================
         * 期待仕様：
         * - 今日が 2026/01/21 なら month='2026-01' を最優先表示
         * - 今月レコードが無ければ「最新の公開済み」を表示（フォールバック）
         * - カバー（monthly_item_cover）を eager load
         * - ログインユーザーのメッセージ（feedback_posts）投稿済み判定で CTA 切替
         */
        $now = now();
        $currentMonth = $now->format('Y-m');

        $baseQuery = MonthlyItem::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->with([
                'mediaFiles' => fn ($q) => $q
                    ->where('media_files.type', 'monthly_item_cover')
                    ->orderBy('media_relations.sort_order'),
            ]);

        // 1) 今月（month一致）を最優先
        $monthlyItem = (clone $baseQuery)
            ->where('month', $currentMonth)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();

        // 2) フォールバック（不要ならこの if ブロックごと削除）
        if (!$monthlyItem) {
            $monthlyItem = $baseQuery
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->first();
        }

        $existingMessage = null;
        $hasMessage = false;

        if ($monthlyItem && $user) {
            $existingMessage = FeedbackPost::query()
                ->where('monthly_item_id', $monthlyItem->id)
                ->where('user_id', $user->id)
                ->first();

            $hasMessage = (bool) $existingMessage;
        }

        // カバー（monthly_item_cover は eager load 済み想定）
        $monthlyCover = $monthlyItem?->mediaFiles?->first();

        // バッジ文言（フィードバック→メッセージ表記）
        $monthlyBadge = 'badge-ghost';
        $monthlyLabel = '公開中';

        if ($monthlyItem) {
            if ($monthlyItem->isFeedbackOpen()) {
                $monthlyBadge = 'badge-primary animate-pulse';
                $monthlyLabel = 'メッセージ受付中';
            } elseif ($monthlyItem->status === 'published' && $monthlyItem->feedback_start_at && now()->lt($monthlyItem->feedback_start_at)) {
                $monthlyBadge = 'badge-warning';
                $monthlyLabel = 'メッセージ受付開始前';
            } elseif ($monthlyItem->isFeedbackClosed()) {
                $monthlyBadge = 'badge-neutral';
                $monthlyLabel = 'メッセージ受付終了';
            }
        }

        // CTA（未投稿なら create / 投稿済みなら edit）
        $monthlyActionUrl = null;
        $monthlyActionLabel = null;

        if ($monthlyItem && $user) {
            if ($existingMessage) {
                $monthlyActionUrl = route('monthly-items.feedback.edit', $monthlyItem);
                $monthlyActionLabel = 'メッセージを編集する';
            } else {
                $monthlyActionUrl = route('monthly-items.feedback.create', $monthlyItem);
                $monthlyActionLabel = 'メッセージを書く';
            }
        }

        return view('livewire.dashboard.index', [
            'user' => $user,
            'page' => $page,
            'rooms' => $rooms,
            'latestPosts' => $latestPosts,
            'showPaidIntroBanners' => $showPaidIntroBanners,

            // monthly
            'monthlyItem' => $monthlyItem,
            'hasMessage' => $hasMessage,
            'existingMessage' => $existingMessage,
            'monthlyCover' => $monthlyCover,
            'monthlyBadge' => $monthlyBadge,
            'monthlyLabel' => $monthlyLabel,
            'monthlyActionUrl' => $monthlyActionUrl,
            'monthlyActionLabel' => $monthlyActionLabel,
        ])->layout('layouts.app');
    }
}
