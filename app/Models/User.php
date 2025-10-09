<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'member_number',
        'name',
        'last_name',
        'first_name_kana',
        'last_name_kana',
        'display_name',
        'instagram_id',
        'avatar_media_id',
        'company_name',
        'postal_code',
        'prefecture',
        'address1',
        'address2',
        'address3',
        'country',
        'phone',
        'email',
        'role',
        'user_type',
        'user_status',
        'email_notification',
        'remarks',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_notification' => 'boolean',
        ];
    }
    
    public function avatar()
    {
        return $this->belongsTo(MediaFile::class, 'avatar_media_id');
    }
    
    public function mediaFiles()
    {
        return $this->morphToMany(MediaFile::class, 'mediable', 'media_relations')
                    ->withPivot('sort_order')
                    ->orderBy('sort_order');
    }

}