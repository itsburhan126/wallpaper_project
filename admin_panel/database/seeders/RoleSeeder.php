<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin role
        // We don't need to assign specific permissions because the RoleController and Middleware 
        // bypass checks for 'super-admin' slug.
        Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'permissions' => [], 
            ]
        );
    }
}
