<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'notifiable_id',
        'notifiable_type',
        'type',
        'message',
        'read_at',
        'room_id',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
    
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
