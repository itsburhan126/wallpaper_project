<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'redeem_gateway_id',
        'name',
        'coin_cost',
        'amount',
        'currency',
        'input_hint',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'coin_cost' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function gateway()
    {
        return $this->belongsTo(RedeemGateway::class, 'redeem_gateway_id');
    }
}
