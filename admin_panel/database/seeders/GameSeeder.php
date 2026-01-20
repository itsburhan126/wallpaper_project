<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            // Action
            ['title' => 'Space Shooter', 'image' => 'games/space_shooter.png'],
            ['title' => 'Zombie Hunter', 'image' => 'games/zombie_hunter.png'],
            // Adventure
            ['title' => 'Jungle Run', 'image' => 'games/jungle_run.png'],
            ['title' => 'Island Survival', 'image' => 'games/island_survival.png'],
            // Puzzle
            ['title' => 'Block Master', 'image' => 'games/block_master.png'],
            ['title' => 'Sudoku Pro', 'image' => 'games/sudoku_pro.png'],
            // Strategy
            ['title' => 'Tower Defense', 'image' => 'games/tower_defense.png'],
            ['title' => 'Clash of Kings', 'image' => 'games/clash_kings.png'],
            // Sports
            ['title' => 'Soccer Star', 'image' => 'games/soccer_star.png'],
            ['title' => 'Basket Dunk', 'image' => 'games/basket_dunk.png'],
            // Racing
            ['title' => 'Speed Racer', 'image' => 'games/speed_racer.png'],
            ['title' => 'Moto X3M', 'image' => 'games/moto_x3m.png'],
            // RPG
            ['title' => 'Hero Quest', 'image' => 'games/hero_quest.png'],
            ['title' => 'Dragon Slayer', 'image' => 'games/dragon_slayer.png'],
            // Arcade
            ['title' => 'Pac Maze', 'image' => 'games/pac_maze.png'],
            ['title' => 'Brick Breaker', 'image' => 'games/brick_breaker.png'],
            // Simulation
            ['title' => 'Farm Life', 'image' => 'games/farm_life.png'],
            ['title' => 'City Builder', 'image' => 'games/city_builder.png'],
            // Board
            ['title' => 'Chess Master', 'image' => 'games/chess_master.png'],
            ['title' => 'Ludo King', 'image' => 'games/ludo_king.png'],
        ];

        foreach ($games as $game) {
            DB::table('games')->updateOrInsert(
                ['title' => $game['title']],
                [
                    'description' => 'This is a description for ' . $game['title'],
                    'image' => $game['image'],
                    'url' => 'https://example.com/games/' . str_replace(' ', '-', strtolower($game['title'])),
                    'play_time' => 60,
                    'win_reward' => rand(10, 100),
                    'is_active' => true,
                    'is_featured' => rand(0, 1) == 1,
                    'total_plays' => rand(100, 1000),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
