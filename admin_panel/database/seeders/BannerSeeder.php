<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [];
        for ($i = 1; $i <= 10; $i++) {
            $banners[] = [
                'title' => 'Banner ' . $i,
                'image' => 'banners/banner_' . $i . '.png',
                'link' => 'https://example.com/promo/' . $i,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('banners')->insert($banners);
    }
}
