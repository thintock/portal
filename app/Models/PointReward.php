<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointReward extends Model
{
    use HasFactory;

    protected $table = 'point_rewards';

    protected $fillable = [
        'name',           // 交換商品名
        'description',    // 説明
        'points_cost',    // 必要ポイント
        'stock',          // 在庫（null=無制限）
        'is_active',      // 公開/非公開
        'sort_order',     // 表示順
    ];

    protected $casts = [
        'points_cost' => 'integer',
        'stock'       => 'integer',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    public function redemptions()
    {
        return $this->hasMany(PointRedemption::class, 'point_reward_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}