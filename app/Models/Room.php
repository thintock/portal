<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'cover_image',
        'visibility',
        'post_policy',
        'owner_id',
        'sort_order',
        'is_active',
        'posts_count',
        'members_count',
        'last_posted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_posted_at' => 'datetime',
        'posts_count' => 'integer',
        'members_count' => 'integer',
    ];

    // --- リレーション ---
    
    // ルームの作成者（オーナー）
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // * ルームに属する投稿
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    // ルームに所属するメンバー
    public function members()
    {
        return $this->hasMany(RoomMember::class);
    }
    
    public function membersCount()
    {
        return $this->members()->count();
    }
    
    public function memberUsers()
{
    return $this->belongsToMany(User::class, 'room_members')
        ->withPivot('role', 'joined_at')
        ->inRandomOrder(); // ランダム並び
}

}
