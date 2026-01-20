<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method_id',
        'gateway_name',
        'coin_cost',
        'amount',
        'currency',
        'account_details',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'coin_cost' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function method()
    {
        return $this->belongsTo(RedeemMethod::class, 'method_id');
    }
}
