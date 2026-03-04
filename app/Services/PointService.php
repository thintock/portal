<?php

namespace App\Services;

use App\Models\PointRule;
use App\Models\RoomPointRule;
use App\Models\PointLedger;
use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PointService
{
    /**
     * 付与（earn）
     * - subject は Post/Comment/Order 等の Eloquent Model
     * - room は任意（投稿/コメントなら room_id を渡す）
     * - 二重付与防止：unique_point_action により同一subject+actionの earn は1回だけ
     */
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

    /**
     * 取消（revoke）
     * - 対象 subject の earn を探し、その delta 分だけマイナス台帳を切る
     * - すでに revoke 済みならスキップ
     * - SoftDelete/ForceDelete どちらでも呼べる
     */
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

    /**
     * 交換（redeem）
     * - 現在残高から差し引きたいだけなら ledger に redeem(-points) を切るだけでOK
     * - 在庫や承認フローは point_redemptions を使う（ここではledgerのみ）
     */
    public function redeem(
        User $user,
        int $points,
        ?Model $subject = null,
        ?array $meta = null,
        ?Carbon $now = null
    ): PointLedger {
        $now ??= now();

        if ($points <= 0) {
            throw new \InvalidArgumentException('points must be positive.');
        }

        // 残高チェック（期限切れ除外）
        $balance = $this->balance($user, $now);
        if ($balance < $points) {
            throw new \RuntimeException('insufficient points.');
        }

        return DB::transaction(function () use ($user, $points, $subject, $meta, $now) {
            return PointLedger::create([
                'user_id'      => $user->id,
                'delta'        => -1 * abs($points),
                'reason'       => 'redeem',
                'action_type'  => null,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id'   => $subject?->getKey(),
                'room_id'      => null,
                'expires_at'   => null,
                'meta_json'    => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        });
    }

    /**
     * 残高（期限内の earn + revoke + redeem の合計）
     * - earn は expires_at を見る
     * - revoke/redeem は expires_at null 想定なので常に計上される
     */
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