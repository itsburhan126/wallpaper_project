<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortComment extends Model
{
    protected $fillable = ['user_id', 'short_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function short()
    {
        return $this->belongsTo(Short::class);
    }
}
