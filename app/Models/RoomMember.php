<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'role',
        'joined_at',
        'muted',
        'notifications',
    ];

    protected $casts = [
        'muted' => 'boolean',
        'notifications' => 'array',
        'joined_at' => 'datetime',
    ];

    // --- リレーション ---

    /**
     * 所属するルーム
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * ルームメンバーのユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
