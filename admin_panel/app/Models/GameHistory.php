<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameHistory extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'coins',
        'status',
        'played_at'
    ];
}
