<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointRule;
use App\Models\Room;
use App\Models\RoomPointRule;
use App\Models\User;
use App\Models\PointLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointSettingsController extends Controller
{
    private function actions(): array
    {
        // config/points.php の actions を想定
        // 例: ['post.created' => '投稿（作成）', ...]
        return (array) config('points.actions', []);
    }

    public function index()
    {
        $actions = $this->actions();

        // 既存ルール（全体）
        $rules = PointRule::query()
            ->orderBy('action_type')
            ->get()
            ->keyBy('action_type');

        // 画面用：actionsのキーで揃えた行データ
        $rows = collect($actions)->map(function ($label, $actionType) use ($rules) {
            $r = $rules->get($actionType);

            return [
                'action_type' => $actionType,
                'label'       => $label,
                'points'      => $r?->base_points ?? 0,
                'is_active'   => (bool) ($r?->is_active ?? true),
            ];
        })->values();

        // ルーム別上書き件数（参考表示）
        $roomOverrideCounts = RoomPointRule::query()
            ->select('action_type', DB::raw('COUNT(*) as cnt'))
            ->groupBy('action_type')
            ->pluck('cnt', 'action_type')
            ->toArray();
        $now = now();
    
        // 全ユーザーに付与している「現在有効なポイント合計（残高合計）」
        // ※ delta が +付与 / -取消・交換 なので、単純に合計でOK
        $totalOutstandingPoints = (int) PointLedger::query()
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', $now);
            })
            ->sum('delta');
    
        // ユーザー別「現在有効なポイント残高」一覧（30件ページネーション）
        $balancesSub = PointLedger::query()
            ->select('user_id', DB::raw('SUM(delta) as point_balance'))
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', $now);
            })
            ->groupBy('user_id');
    
        $usersWithPoints = User::query()
            ->leftJoinSub($balancesSub, 'pl', function ($join) {
                $join->on('users.id', '=', 'pl.user_id');
            })
            ->select([
                'users.*',
                DB::raw('COALESCE(pl.point_balance, 0) as point_balance'),
            ])
            ->orderByDesc('point_balance')
            ->orderBy('users.id')
            ->paginate(30)
            ->withQueryString();
            
        return view('admin.points.index', compact(
            'rows',
            'roomOverrideCounts',
            'totalOutstandingPoints',
            'usersWithPoints'
        ));
    }

    public function bulkUpdate(Request $request)
    {
        $actions = $this->actions();
        $data = $request->input('rules', []);

        // 最低限のバリデーション（キーは actions にあるものだけ許可）
        foreach ($data as $actionType => $payload) {
            if (!array_key_exists($actionType, $actions)) {
                unset($data[$actionType]);
                continue;
            }
        }

        DB::transaction(function () use ($data) {
            foreach ($data as $actionType => $payload) {
                $points = isset($payload['points']) ? (int) $payload['points'] : 0;
                $isActive = isset($payload['is_active']) ? (bool) $payload['is_active'] : false;

                PointRule::updateOrCreate(
                    ['action_type' => $actionType],
                    ['base_points' => $points, 'is_active' => $isActive]
                );
            }
        });

        return redirect()->route('admin.points.index')->with('success', 'ポイント設定（全体）を保存しました。');
    }

    public function rooms()
    {
        $actions = $this->actions();

        // ルーム一覧 + 上書き数
        $rooms = Room::query()
            ->orderBy('id')
            ->get();

        $overrideCountsByRoom = RoomPointRule::query()
            ->select('room_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('room_id')
            ->pluck('cnt', 'room_id')
            ->toArray();

        return view('admin.points.rooms', compact('rooms', 'overrideCountsByRoom', 'actions'));
    }

    public function room(Room $room)
    {
        $actions = $this->actions();

        $rules = RoomPointRule::query()
            ->where('room_id', $room->id)
            ->get()
            ->keyBy('action_type');

        // 画面用
        $rows = collect($actions)->map(function ($label, $actionType) use ($rules) {
            $r = $rules->get($actionType);
            return [
                'action_type' => $actionType,
                'label'       => $label,
                'points'      => $r?->points_override,        // null = 未設定（全体にフォールバック）
                'is_active'   => $r ? (bool) $r->is_active : null, // null = 未設定
            ];
        })->values();

        return view('admin.points.room', compact('room', 'rows'));
    }

    public function roomBulkUpdate(Request $request, Room $room)
    {
        $actions = $this->actions();
        $data = $request->input('rules', []);

        // actions 以外のキーを排除
        foreach ($data as $actionType => $payload) {
            if (!array_key_exists($actionType, $actions)) {
                unset($data[$actionType]);
            }
        }

        DB::transaction(function () use ($data, $room) {
            foreach ($data as $actionType => $payload) {
    
                $reset = isset($payload['reset']) ? (bool) $payload['reset'] : false;
                if ($reset) {
                    RoomPointRule::where('room_id', $room->id)
                        ->where('action_type', $actionType)
                        ->delete();
                    continue;
                }
    
                // ★空欄は NULL（未設定）として保存
                $raw = $payload['points'] ?? null;
                $pointsOverride = ($raw === null || $raw === '') ? null : (int) $raw;
    
                // is_active：チェックなしなら 0 になるよう hidden を置く想定
                $isActive = isset($payload['is_active']) ? (bool) $payload['is_active'] : true;
    
                RoomPointRule::updateOrCreate(
                    ['room_id' => $room->id, 'action_type' => $actionType],
                    [
                        'points_override' => $pointsOverride, // ★ここ
                        'is_active'       => $isActive,
                    ]
                );
            }
        });

        return redirect()->route('admin.points.room', $room)->with('success', 'ルーム別ポイント設定を保存しました。');
    }
}