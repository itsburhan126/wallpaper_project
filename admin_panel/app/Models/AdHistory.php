<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ad_network',
        'coins',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
