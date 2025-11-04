<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'slug',
        'body1',
        'body2',
        'body3',
        'status',
        'created_by',
        'updated_by',
        ];
        
        // 作成ユーザーとのリレーション
        public function creator()
        {
            return $this->belongsTo(User::class, 'created_by');
        }
        
        // 更新ユーザーとのリレーション
        public function updater()
        {
            return $this->belongsTo(User::class, 'updated_by');
        }
}
