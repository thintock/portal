<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MonthlyItem extends Model
{
    use HasFactory;

    protected $table = 'monthly_items';

    /**
     * 一括代入可能カラム
     */
    protected $fillable = [
        'month',
        'title',
        'description',
        'published_at',
        'feedback_start_at',
        'feedback_end_at',
        'protein',
        'ash',
        'absorption',
        'status',
    ];

    /**
     * 日時キャスト
     */
    protected $casts = [
        'published_at'      => 'datetime',
        'feedback_start_at' => 'datetime',
        'feedback_end_at'   => 'datetime',
    ];

    /* =====================================================
     * Relations
     * ===================================================== */

    /**
     * フィードバック投稿
     */
    public function feedbackPosts()
    {
        return $this->hasMany(FeedbackPost::class, 'monthly_item_id');
    }

    /**
     * 紐づくメディア（画像など）
     */
    public function mediaRelations()
    {
        return $this->morphMany(MediaRelation::class, 'mediable');
    }

    /**
     * メディアファイル（並び順考慮）
     */
    public function mediaFiles()
    {
        return $this->morphToMany(
            MediaFile::class,
            'mediable',
            'media_relations',
            'mediable_id',
            'media_file_id'
        )->withPivot('sort_order')
         ->orderBy('media_relations.sort_order');
    }

    /* =====================================================
     * Accessors / Helpers
     * ===================================================== */

    /**
     * 公開中かどうか
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at
            && now()->greaterThanOrEqualTo($this->published_at);
    }

    /**
     * フィードバック受付中かどうか
     */
    public function isFeedbackOpen(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if (!$this->feedback_start_at || !$this->feedback_end_at) {
            return false;
        }

        return now()->between(
            $this->feedback_start_at,
            $this->feedback_end_at
        );
    }

    /**
     * フィードバック受付終了済みか
     */
    public function isFeedbackClosed(): bool
    {
        return $this->feedback_end_at
            && now()->greaterThan($this->feedback_end_at);
    }

    /**
     * フィードバック件数（index 用）
     */
    public function feedbackCount(): int
    {
        return $this->feedbackPosts()->count();
    }

    /**
     * 成分表示用（% 付き）
     */
    public function proteinLabel(): ?string
    {
        return $this->protein !== null ? number_format($this->protein, 1) . '%' : null;
    }

    public function ashLabel(): ?string
    {
        return $this->ash !== null ? number_format($this->ash, 2) . '%' : null;
    }

    public function absorptionLabel(): ?string
    {
        return $this->absorption !== null ? number_format($this->absorption, 1) . '%' : null;
    }
}
