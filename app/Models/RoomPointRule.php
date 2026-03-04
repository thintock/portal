<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPointRule extends Model
{
    use HasFactory;

    protected $table = 'room_point_rules';

    protected $fillable = [
        'room_id',
        'action_type',      // post.created / comment.created etc
        'points_override',  // このroomでの上書きポイント
        'is_active',
        'note',
    ];

    protected $casts = [
        'room_id'         => 'integer',
        'points_override' => 'integer',
        'is_active'       => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}