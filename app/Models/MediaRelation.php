<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaRelation extends Model
{
    protected $fillable = [
        'media_file_id',
        'mediable_id',
        'mediable_type',
        'sort_order',
    ];

    /**
     * 紐づく MediaFile
     */
    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class, 'media_file_id');
    }

    /**
     * 紐づく対象モデル（Post, Comment, etc...）
     */
    public function mediable()
    {
        return $this->morphTo();
    }
}
