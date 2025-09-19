<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberNumberHistory extends Model
{
    protected $fillable = [
        'user_id',
        'number',
        'assigned_at',
    ];

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
