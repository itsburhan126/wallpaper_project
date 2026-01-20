<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wallpaper;

class WallpaperSeeder extends Seeder
{
    public function run(): void
    {
        Wallpaper::factory()->count(100)->create();
    }
}
