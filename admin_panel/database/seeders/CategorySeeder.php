<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
             
            // New Categories
            ['name' => 'Abstract', 'image' => 'categories/abstract.png'],
            ['name' => 'Nature', 'image' => 'categories/nature.png'],
            ['name' => 'Cars', 'image' => 'categories/cars.png'],
            ['name' => 'Space', 'image' => 'categories/space.png'],
            ['name' => 'Animals', 'image' => 'categories/animals.png'],
            ['name' => 'Anime', 'image' => 'categories/anime.png'],
            ['name' => 'Minimal', 'image' => 'categories/minimal.png'],
            ['name' => 'Tech', 'image' => 'categories/tech.png'],
            ['name' => 'Music', 'image' => 'categories/music.png'],
            ['name' => 'Dark', 'image' => 'categories/dark.png'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                [
                    'image' => $category['image'],
                    'status' => true,
                    'updated_at' => now(),
                    // created_at handled by DB default or we can leave it
                ]
            );
        }
    }
}
