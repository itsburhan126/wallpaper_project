<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define all permissions
        $permissions = [
            'dashboard_access',
            'manage_banners',
            'manage_settings',
            'manage_pages',
            'manage_staff',
        ];

        // Create Demo Admin Role
        $role = Role::create([
            'name' => 'Demo Admin',
            'slug' => 'demo-admin',
            'permissions' => $permissions, // Full view access
        ]);

        // Create Demo Admin User
        Admin::create([
            'name' => 'Demo Admin',
            'email' => 'demo@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => $role->id,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
