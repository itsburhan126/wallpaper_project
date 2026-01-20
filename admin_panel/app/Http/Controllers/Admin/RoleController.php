<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    protected $permissions = [
        'dashboard_access' => 'Access Dashboard',
        'manage_users' => 'Manage Users',
        'manage_banners' => 'Manage Banners',
        'manage_settings' => 'Manage Settings',
        'manage_pages' => 'Manage Pages',
        'manage_staff' => 'Manage Staff & Roles',
    ];

    public function index()
    {
        $roles = Role::withCount('admins')->latest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->permissions;
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
        ], [
            'name.unique' => 'This role name already exists.',
        ]);

        Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        if ($role->slug === 'super-admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be edited.');
        }

        $permissions = $this->permissions;
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->slug === 'super-admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be edited.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be deleted.');
        }

        if ($role->admins()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete role assigned to staff members.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
