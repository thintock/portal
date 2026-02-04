<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavedPostCategory extends Model
{
    protected $table = 'saved_post_categories';

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savedPosts(): HasMany
    {
        return $this->hasMany(SavedPost::class, 'saved_post_category_id');
    }
}
