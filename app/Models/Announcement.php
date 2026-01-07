<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'visibility',
        'publish_start_at',
        'publish_end_at',
        'user_id',
    ];

    protected $casts = [
        'publish_start_at' => 'datetime',
        'publish_end_at'   => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * MediaRelation / MediaFile（Event と同じ持ち方）
     * type を 'announcement_cover' / 'announcement_gallery' のように分ける運用が扱いやすいです。
     */
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

    /**
     * 公開中（公開期間の窓に入っている）
     * - start NULL: 開始制限なし
     * - end   NULL: 終了制限なし
     */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where(function ($q) {
            $q->whereNull('publish_start_at')
              ->orWhere('publish_start_at', '<=', now());
        })->where(function ($q) {
            $q->whereNull('publish_end_at')
              ->orWhere('publish_end_at', '>', now());
        });
    }

    public function scopeVisibleTo(Builder $q, ?\App\Models\User $user): Builder
    {
        $now = now();

        $q->where(function ($qq) use ($now) {
            // 開始未設定 or 開始 <= now
            $qq->whereNull('publish_start_at')
               ->orWhere('publish_start_at', '<=', $now);
        })->where(function ($qq) use ($now) {
            // 終了未設定 or 終了 >= now
            $qq->whereNull('publish_end_at')
               ->orWhere('publish_end_at', '>=', $now);
        });

        // 未ログイン（今回は dashboard 配下想定なので基本来ないが念のため）
        if (!$user) {
            return $q->where('visibility', 'public');
        }

        if ($user->role === 'admin') {
            // admin は全部見える（期間フィルタは維持）
            return $q->whereIn('visibility', ['public', 'membership', 'admin']);
        }

        // 課金中なら membership も可
        if ($user->subscribed('default')) {
            return $q->whereIn('visibility', ['public', 'membership']);
        }

        // それ以外は public のみ
        return $q->where('visibility', 'public');
    }

    public function getIsLiveAttribute(): bool
    {
        $startOk = is_null($this->publish_start_at) || $this->publish_start_at->lte(now());
        $endOk   = is_null($this->publish_end_at) || $this->publish_end_at->gt(now());
        return $startOk && $endOk;
    }
}
