<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Cashier\Billable;
use App\Notifications\CustomVerifyEmail;

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
        'first_name',
        'last_name',
        'first_name_kana',
        'last_name_kana',
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
        'birthday_month',
        'birthday_day',
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
    
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
    
    // 現在の会員番号
    public function currentMemberNumber(): ?int
    {
        return MemberNumberHistory::where('user_id', $this->id)
            ->orderByDesc('assigned_at') // 明示的に最新
            ->value('number');
    }
    
    // User.php
    public function latestMemberNumberHistory()
    {
        return $this->hasOne(MemberNumberHistory::class)
            ->latestOfMany('assigned_at');
    }

    public function hasActiveSubscription(): bool
    {
        $subscription = $this->subscription('default');
    
        return $subscription && $subscription->stripe_status === 'active';
    }

}