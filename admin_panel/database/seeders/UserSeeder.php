<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->updateOrInsert(
                ['email' => 'user' . $i . '@example.com'],
                [
                    'name' => 'User ' . $i,
                    'password' => Hash::make('password'),
                    'coins' => rand(100, 5000),
                    'gems' => rand(10, 500),
                    'level' => rand(1, 10),
                    'referral_code' => Str::random(8),
                    'status' => 1,
                    'updated_at' => now(),
                    // created_at optional, let's leave it
                ]
            );
        }
    }
}
