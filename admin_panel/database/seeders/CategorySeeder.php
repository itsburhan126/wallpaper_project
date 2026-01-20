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
            ['name' => 'Action', 'image' => 'categories/action.png'],
            ['name' => 'Adventure', 'image' => 'categories/adventure.png'],
            ['name' => 'Puzzle', 'image' => 'categories/puzzle.png'],
            ['name' => 'Strategy', 'image' => 'categories/strategy.png'],
            ['name' => 'Sports', 'image' => 'categories/sports.png'],
            ['name' => 'Racing', 'image' => 'categories/racing.png'],
            ['name' => 'RPG', 'image' => 'categories/rpg.png'],
            ['name' => 'Arcade', 'image' => 'categories/arcade.png'],
            ['name' => 'Simulation', 'image' => 'categories/simulation.png'],
            ['name' => 'Board', 'image' => 'categories/board.png'],
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
