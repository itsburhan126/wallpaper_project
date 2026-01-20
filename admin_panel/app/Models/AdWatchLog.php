<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdWatchLog extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'ad_type',
        'status',
        'reward_type',
        'reward_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
