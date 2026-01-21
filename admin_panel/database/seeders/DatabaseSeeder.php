<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            PageSeeder::class,
            CategorySeeder::class,
            WallpaperSeeder::class,
            GameSeeder::class,
            BannerSeeder::class,
            RedeemSeeder::class,
            UserSeeder::class,
        ]);
    }
}
