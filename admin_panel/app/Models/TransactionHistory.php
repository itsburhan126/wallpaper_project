<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'source',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
