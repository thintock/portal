<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointRedemption extends Model
{
    use HasFactory;

    protected $table = 'point_redemptions';

    protected $fillable = [
        'user_id',
        'point_reward_id',
        'points_used',
        'status',        // requested / approved / shipped / completed / canceled など
        'note',          // 運営メモ
    ];

    protected $casts = [
        'user_id'      => 'integer',
        'point_reward_id'    => 'integer',
        'points_cost'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(PointReward::class, 'point_reward_id');
    }

    public function ledgers()
    {
        // redemption を subject にした ledger を引けるようにする（任意）
        return $this->morphMany(PointLedger::class, 'subject');
    }
}