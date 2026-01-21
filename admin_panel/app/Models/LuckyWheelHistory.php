<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuckyWheelHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
