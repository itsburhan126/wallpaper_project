<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image', 'url', 
        'play_time', 'win_reward', 
        'is_active', 'total_plays', 'is_featured'
    ];
}
