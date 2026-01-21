<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class FeedbackPost extends Model
{
    protected $fillable = [
        'monthly_item_id',
        'user_id',
        'title',
        'body',
    ];

    public function monthlyItem(): BelongsTo
    {
        return $this->belongsTo(MonthlyItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * media_relations（この投稿に紐づく中間）
     */
    public function mediaRelations(): MorphMany
    {
        return $this->morphMany(MediaRelation::class, 'mediable')
            ->orderBy('sort_order');
    }

    /**
     * 添付メディア（複数）
     */
    public function mediaFiles(): MorphToMany
    {
        return $this->morphToMany(
                MediaFile::class,
                'mediable',
                'media_relations',
                'mediable_id',
                'media_file_id'
            )
            ->withPivot(['sort_order'])
            ->orderBy('media_relations.sort_order');
    }
}
