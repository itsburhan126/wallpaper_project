<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    protected $appends = ['image'];

    public function getImageAttribute()
    {
        if (!$this->icon) {
            return null;
        }
        if (filter_var($this->icon, FILTER_VALIDATE_URL)) {
            return $this->icon;
        }
        return asset($this->icon);
    }

    public function methods()
    {
        return $this->hasMany(RedeemMethod::class, 'redeem_gateway_id');
    }
}
