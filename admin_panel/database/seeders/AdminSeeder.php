<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = \App\Models\Role::where('slug', 'super-admin')->first();

        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@gmail.com');
        $password = env('SUPER_ADMIN_PASSWORD', '123456');

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'role_id' => $superAdminRole ? $superAdminRole->id : null,
            ]
        );

        // Keep the original admin if needed, or remove it. 
        // I'll leave it but ensure it doesn't conflict or just update it if it was the intended super admin.
        // Actually, let's just add the Super Admin as requested.
    }
}
