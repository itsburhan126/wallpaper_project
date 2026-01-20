<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Premium Collection',
                'image' => 'https://picsum.photos/800/400?random=1',
                'link' => 'https://example.com/premium',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'New Arrivals',
                'image' => 'https://picsum.photos/800/400?random=2',
                'link' => 'https://example.com/new',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Editor Choice',
                'image' => 'https://picsum.photos/800/400?random=3',
                'link' => 'https://example.com/choice',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Trending Now',
                'image' => 'https://picsum.photos/800/400?random=4',
                'link' => 'https://example.com/trending',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Special Offer',
                'image' => 'https://picsum.photos/800/400?random=5',
                'link' => 'https://example.com/offer',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('banners')->insert($banners);
    }
}
