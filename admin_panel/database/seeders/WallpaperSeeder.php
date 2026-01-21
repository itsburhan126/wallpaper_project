<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WallpaperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallpapers = [];
        // Assuming 20 categories exist with IDs 1-20 (since we seeded them)
        // We want 100 wallpapers
        
        for ($i = 1; $i <= 100; $i++) {
            $categoryId = rand(1, 20); // Random category between 1 and 20
            $wallpapers[] = [
                'category_id' => $categoryId,
                'image' => 'wallpapers/wallpaper_' . $i . '.jpg',
                'thumbnail' => 'wallpapers/thumb_' . $i . '.jpg',
                'tags' => 'tag1,tag2,nature,abstract',
                'downloads' => rand(0, 1000),
                'views' => rand(0, 5000),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Chunk insert to avoid potential issues with large query
        foreach (array_chunk($wallpapers, 50) as $chunk) {
            DB::table('wallpapers')->insert($chunk);
        }
    }
}
