<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DailyReward;

class DailyRewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rewards = [
            ['day' => 1, 'coins' => 100, 'gems' => 0],
            ['day' => 2, 'coins' => 200, 'gems' => 0],
            ['day' => 3, 'coins' => 300, 'gems' => 0],
            ['day' => 4, 'coins' => 400, 'gems' => 0],
            ['day' => 5, 'coins' => 500, 'gems' => 10],
            ['day' => 6, 'coins' => 600, 'gems' => 0],
            ['day' => 7, 'coins' => 700, 'gems' => 0],
            ['day' => 8, 'coins' => 800, 'gems' => 0],
            ['day' => 9, 'coins' => 900, 'gems' => 0],
            ['day' => 10, 'coins' => 1000, 'gems' => 20],
            ['day' => 11, 'coins' => 1200, 'gems' => 0],
            ['day' => 12, 'coins' => 1500, 'gems' => 0],
            ['day' => 13, 'coins' => 1800, 'gems' => 0],
            ['day' => 14, 'coins' => 2000, 'gems' => 0],
            ['day' => 15, 'coins' => 5000, 'gems' => 50],
        ];

        foreach ($rewards as $reward) {
            DailyReward::updateOrCreate(['day' => $reward['day']], $reward);
        }
    }
}
