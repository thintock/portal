<?php

namespace App\Services;

use App\Models\PointRule;
use App\Models\RoomPointRule;
use App\Models\PointLedger;
use App\Models\PointRedemption;
use App\Models\PointReward;
use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PointService
{
    // ポイント付与（earn）
    public function earn(
        User $user,
        string $actionType,
        ?Model $subject = null,
        ?int $roomId = null,
        ?int $pointsOverride = null,
        ?array $meta = null,
        ?Carbon $now = null
    ): ?PointLedger {
        $now ??= now();
        
        $points = $pointsOverride ?? $this->resolvePoints($actionType, $roomId);
        
        if ($points === null || $points === 0) {
            return null; // 無効 or 0ポイントは何もしない
        }

        $expiresAt = $now->copy()->addDays((int) config('points.default_expire_days', 365));

        return DB::transaction(function () use ($user, $actionType, $subject, $roomId, $points, $expiresAt, $meta, $now) {
            // すでに earn 済みならスキップ（DB uniqueでも落ちないように先に確認）
            if ($subject) {
                $exists = PointLedger::query()
                    ->where('reason', 'earn')
                    ->where('action_type', $actionType)
                    ->where('subject_type', $subject->getMorphClass())
                    ->where('subject_id', $subject->getKey())
                    ->exists();
                if ($exists) return null;
            }

            return PointLedger::create([
                'user_id'      => $user->id,
                'delta'        => (int) $points,   // +points
                'reason'       => 'earn',
                'action_type'  => $actionType,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id'   => $subject?->getKey(),
                'room_id'      => $roomId,
                'expires_at'   => $expiresAt,
                'meta_json'    => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        });
    }

    //ポイント取消
    public function revoke(
        User $user,
        string $actionType,
        Model $subject,
        ?array $meta = null,
        ?Carbon $now = null
    ): ?PointLedger {
        $now ??= now();

        return DB::transaction(function () use ($user, $actionType, $subject, $meta, $now) {
            $subjectType = $subject->getMorphClass();
            $subjectId   = $subject->getKey();

            // 対象 earn を探す
            $earn = PointLedger::query()
                ->where('reason', 'earn')
                ->where('action_type', $actionType)
                ->where('subject_type', $subjectType)
                ->where('subject_id', $subjectId)
                ->orderByDesc('id')
                ->first();

            if (! $earn) {
                return null; // 付与が無いなら取り消せない
            }

            // すでに revoke 済みなら何もしない
            $revoked = PointLedger::query()
                ->where('reason', 'revoke')
                ->where('action_type', $actionType)
                ->where('subject_type', $subjectType)
                ->where('subject_id', $subjectId)
                ->exists();

            if ($revoked) return null;

            return PointLedger::create([
                'user_id'      => $user->id,
                'delta'        => -1 * abs((int) $earn->delta),
                'reason'       => 'revoke',
                'action_type'  => $actionType,
                'subject_type' => $subjectType,
                'subject_id'   => $subjectId,
                'room_id'      => $earn->room_id,
                // 取消自体は期限不要（nullでOK）
                'expires_at'   => null,
                'meta_json'    => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        });
    }

    // 交換
    public function redeem(User $user, PointReward $reward)
    {
        return DB::transaction(function () use ($user, $reward) {
    
            // ユーザーロック（二重交換防止）
            $user = User::whereKey($user->id)
                ->lockForUpdate()
                ->first();
    
            // reward ロック（在庫管理用）
            $reward = PointReward::whereKey($reward->id)
                ->lockForUpdate()
                ->first();
    
            if (!$reward->is_active) {
                throw new \Exception('この景品は現在交換できません。');
            }
    
            if ($reward->stock !== null && $reward->stock <= 0) {
                throw new \Exception('在庫がありません。');
            }
    
            $balance = $this->balance($user);
    
            if ($balance < $reward->points_cost) {
                throw new \Exception('ポイントが不足しています。');
            }
    
            // 在庫減算
            if ($reward->stock !== null) {
                $reward->decrement('stock');
            }
    
            // redemption 作成
            $redemption = PointRedemption::create([
                'user_id' => $user->id,
                'point_reward_id' => $reward->id,
                'points_used' => $reward->points_cost,
                'status' => 'requested'
            ]);
    
            // ledger 記録
            PointLedger::create([
                'user_id' => $user->id,
                'delta' => -$reward->points_cost,
                'reason' => 'redeem',
                'subject_type' => PointRedemption::class,
                'subject_id' => $redemption->id,
                'meta_json' => json_encode([
                    'reward_name' => $reward->name
                ])
            ]);
    
            return $redemption;
        });
    }

    // 残高
    public function balance(User $user, ?Carbon $now = null): int
    {
        $now ??= now();

        return (int) PointLedger::query()
            ->where('user_id', $user->id)
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', $now);
            })
            ->sum('delta');
    }

    /**
     * アクションの付与ポイントを決定
     * 優先順位：
     *  1) room_point_rules（room_id + action_type）
     *  2) point_rules（action_type）
     * 無効化されてたら null
     */
    public function resolvePoints(string $actionType, ?int $roomId = null): ?int
    {
        if ($roomId) {
            $roomRule = RoomPointRule::query()
                ->where('room_id', $roomId)
                ->where('action_type', $actionType)
                ->first();

            if ($roomRule) {
                if (! $roomRule->is_active) return null;
                // override が null でも「無効」ではないので fallback したいならここ変える
                if ($roomRule->points_override !== null) {
                    return (int) $roomRule->points_override;
                }
            }
        }

        $rule = PointRule::query()
            ->where('action_type', $actionType)
            ->first();

        if (! $rule || ! $rule->is_active) return null;

        return (int) $rule->base_points;
    }
}