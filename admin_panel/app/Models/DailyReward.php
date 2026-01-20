<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReward extends Model
{
    protected $fillable = [
        'day',
        'coins',
        'gems',
    ];
}
