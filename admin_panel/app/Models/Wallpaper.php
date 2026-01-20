<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallpaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'image',
        'thumbnail',
        'tags',
        'downloads',
        'views',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
