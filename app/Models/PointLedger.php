<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointLedger extends Model
{
    use HasFactory;

    protected $table = 'point_ledgers';

    protected $fillable = [
        'user_id',
        'delta',        // +付与 / -取消 / -消費（整数）
        'reason',       // 任意の説明（例: "post created", "post deleted", "reward redemption"）
        'action_type',  // post.created / post.deleted / comment.created / comment.deleted / reward.redeemed ...
        // ポリモーフィック参照（投稿/コメント/注文など）
        'subject_type', // App\Models\Post 等
        'subject_id',
        'room_id',      // null可（投稿/コメントは room_id を入れると集計や設定が楽）
        // 有効期限（1年運用）
        'expires_at',
        // 追加情報（注文IDなど）
        'meta_json',
    ];

    protected $casts = [
        'user_id'     => 'integer',
        'delta'       => 'integer',
        'room_id'     => 'integer',
        'expires_at'  => 'datetime',
        'meta_json'   => 'array',
    ];

    // relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    // scopes
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForAction($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    public function scopeForSubject($query, Model $subject)
    {
        return $query->where('subject_type', get_class($subject))
                     ->where('subject_id', $subject->getKey());
    }
}