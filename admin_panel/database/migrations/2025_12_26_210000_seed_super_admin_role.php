<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\Admin;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create Super Admin Role
        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'permissions' => [], // Super admin has all permissions via logic
        ]);

        // Assign to Admin ID 1
        $admin = Admin::find(1);
        if ($admin) {
            $admin->role_id = $role->id;
            $admin->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
