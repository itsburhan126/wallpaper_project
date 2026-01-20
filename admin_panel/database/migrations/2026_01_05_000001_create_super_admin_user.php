<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure Super Admin role exists
        $role = Role::where('slug', 'super-admin')->first();
        if (!$role) {
            $role = Role::create([
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'permissions' => [], // Super admin bypasses via logic
            ]);
        }

        // Create super admin user if not exists
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@gmail.com');
        $password = env('SUPER_ADMIN_PASSWORD', '123456');
        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            $admin = Admin::create([
                'name' => 'Super Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $role->id,
            ]);
        } else {
            // Ensure role assignment
            if ($admin->role_id !== $role->id) {
                $admin->role_id = $role->id;
                $admin->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do not remove super admin on rollback for safety
    }
};
