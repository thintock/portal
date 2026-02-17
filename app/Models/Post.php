<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'user_id',
        'post_type',
        'body',
        'visibility',
        'status',
        'media_json',
        'external_url',
        'link_preview_json',
        'reaction_count',
        'comment_count',
        'pinned_at',
        'last_activity_at',
    ];

    protected $casts = [
        'media_json'        => 'array',
        'link_preview_json' => 'array',
        'pinned_at'         => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * 投稿が属するルーム
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * 投稿者（ユーザー）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * コメント一覧
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * リアクション一覧（いいね等）
     */
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }
    
    /**
     * この投稿に紐づく MediaRelation
     */
    public function mediaRelations()
    {
        return $this->morphMany(MediaRelation::class, 'mediable')
            ->with('mediaFile')
            ->orderBy('sort_order');
    }

    /**
     * 投稿に添付された MediaFile 一覧
     * 直接 MediaFile モデルを取得したい場合に便利
     */
    public function mediaFiles()
    {
        return $this->morphToMany(MediaFile::class, 'mediable', 'media_relations')
                    ->withPivot('sort_order')
                    ->orderBy('sort_order');
    }
    
    // 投稿保存
    public function savedPosts(): HasMany
    {
        return $this->hasMany(\App\Models\SavedPost::class);
    }
}
