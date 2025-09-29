<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'parent_id',
        'root_id',
        'user_id',
        'body',
        'media_json',
        'status',
        'reaction_count',
        'replies_count',
        'depth',
    ];

    protected $casts = [
        'media_json' => 'array',
    ];

    // --- リレーション ---

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function root()
    {
        return $this->belongsTo(Comment::class, 'root_id');
    }
}
