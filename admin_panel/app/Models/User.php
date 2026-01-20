<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'status',
        'password',
        'google_id',
        'avatar',
        'coins',
        'gems',
        'level',
        'referral_code',
        'referred_by',
        'daily_streak',
        'last_daily_reward_at',
    ];

    public function referrals()
    {
        return $this->hasMany(ReferralHistory::class, 'referrer_id');
    }

    public function redeemRequests()
    {
        return $this->hasMany(RedeemRequest::class);
    }

    public function transactions()
    {
        return $this->hasMany(TransactionHistory::class);
    }

    public function adWatches()
    {
        return $this->hasMany(AdWatchLog::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarAttribute($value)
    {
        if ($value === 'default.png') {
            return null;
        }
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }
}
