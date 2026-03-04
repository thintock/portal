<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    use HasFactory;

    protected $table = 'point_rules';

    protected $fillable = [
        'action_type',   // 例: post.created, post.deleted, comment.created, comment.deleted, purchase.paid ...
        'base_points',   // 基準ポイント（整数、+のみ運用想定）
        'is_active',     // 有効/無効
        'note',          // 任意メモ
    ];

    protected $casts = [
        'base_points' => 'integer',
        'is_active'   => 'boolean',
    ];

    // よく使うスコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}