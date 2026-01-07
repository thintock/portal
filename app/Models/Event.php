<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'event_type',
        'body1',
        'body2',
        'body3',
        'start_at',
        'end_at',
        'location',
        'join_url',
        'capacity',
        'recept',
        'status',
        'visibility',
        'user_id',
    ];
    
    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'recept'   => 'boolean',
    ];

    /** 投稿者（管理者・講師） */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** 参加者 */
    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    /** 有効な参加者（status=going） */
    public function activeParticipants()
    {
        return $this->participants()->where('status', 'going');
    }

    /** メディア関連 */
    public function mediaRelations()
    {
        return $this->morphMany(MediaRelation::class, 'mediable')
                    ->with('mediaFile')
                    ->orderBy('sort_order');
    }

    public function mediaFiles()
    {
        return $this->morphToMany(MediaFile::class, 'mediable', 'media_relations')
                    ->withPivot('sort_order')
                    ->orderBy('sort_order');
    }

    /** コメント */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /** 公開範囲による絞り込み */
    public function scopeVisibleTo(Builder $q, ?User $user): Builder
    {
        // 未ログイン → 一般公開のみ
        if (!$user) {
            return $q->where('visibility', 'public');
        }
    
        // 管理者 → 全ての公開イベント（非公開を除く）
        if ($user->role === 'admin') {
            return $q->whereIn('visibility', ['public', 'membership']);
        }
    
        // サブスク課金中ユーザー → public + membership
        if ($user->subscribed('default')) {
            return $q->whereIn('visibility', ['public', 'membership']);
        }
    
        // それ以外（無料会員など）→ public のみ
        return $q->where('visibility', 'public');
    }

    /** 近日開催（終了していない） */
    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('end_at', '>', now())
             ->where('status', 'published');
    }

    /** 終了済み */
    public function scopePast(Builder $q): Builder
    {
        return $q->where('end_at', '<', now())->where('status', 'published');
    }
    
    /** 開催中か（start_at <= now < end_at） */
    public function getIsOngoingAttribute(): bool
    {
        if (!$this->start_at || !$this->end_at) return false;
    
        return $this->start_at->lte(now()) && $this->end_at->gt(now());
    }

    /** ユーザーのタイムゾーンで表示用に */
    public function getStartsAtTzAttribute(): ?Carbon
    {
        $tz = optional(auth()->user())->timezone ?? 'Asia/Tokyo';
        return $this->start_at?->tz($tz);
    }

    public function getEndsAtTzAttribute(): ?Carbon
    {
        $tz = optional(auth()->user())->timezone ?? 'Asia/Tokyo';
        return $this->end_at?->tz($tz);
    }

    /** ログイン中ユーザーが参加中か判定 */
    public function getIsJoinedAttribute(): bool
    {
        $userId = auth()->id();
        if (!$userId) return false;
        return $this->participants()
            ->where('user_id', $userId)
            ->where('status', 'going')
            ->exists();
    }

    /** 定員に達しているか */
    public function getIsFullAttribute(): bool
    {
        // null または 0 は「無制限」
        if (is_null($this->capacity) || $this->capacity === 0) {
            return false;
        }
    
        return $this->activeParticipants()->count() >= $this->capacity;
    }
    
    /**
     * 現在のユーザーにこのイベントが表示可能かどうかを判定
     */
    public function isVisibleTo($user = null): bool
    {
        // 公開
        if ($this->visibility === 'public') {
            return true;
        }
    
        // サブスク会員向け
        if ($this->visibility === 'membership' && $user?->subscribed('default')) {
            return true;
        }
    
        // 管理者は常に閲覧可
        if ($user?->role === 'admin') {
            return true;
        }
    
        // 非公開 or 権限なし
        return false;
    }
}
