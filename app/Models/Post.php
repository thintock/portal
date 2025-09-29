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
    ];

    protected $casts = [
        'media_json'        => 'array',
        'link_preview_json' => 'array',
        'pinned_at'         => 'datetime',
    ];

    // --- リレーション ---
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
