<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Short extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'video_url',
        'thumbnail_url',
        'views',
        'likes',
        'shares',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function likes()
    {
        return $this->belongsToMany(User::class, 'short_likes', 'short_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(ShortComment::class);
    }
}