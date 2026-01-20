<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Admin::with('role')->whereHas('role', function($q) {
            $q->where('slug', '!=', 'super-admin');
        })->orWhereDoesntHave('role')->latest()->get();
        
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $roles = Role::where('slug', '!=', 'super-admin')->get();
        return view('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully.');
    }

    public function edit($id)
    {
        $staff = Admin::findOrFail($id);
        
        if ($staff->hasRole('super-admin')) {
             return redirect()->route('admin.staff.index')->with('error', 'Cannot edit Super Admin.');
        }

        $roles = Role::where('slug', '!=', 'super-admin')->get();
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $staff = Admin::findOrFail($id);
        
        if ($staff->hasRole('super-admin')) {
             return redirect()->route('admin.staff.index')->with('error', 'Cannot edit Super Admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'password' => 'nullable|confirmed|min:6',
            'role_id' => 'required|exists:roles,id',
        ], [
            'role_id.required' => 'Please assign a role to this staff member.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy($id)
    {
        $staff = Admin::findOrFail($id);
        
        if ($staff->hasRole('super-admin')) {
             return redirect()->route('admin.staff.index')->with('error', 'Cannot delete Super Admin.');
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully.');
    }
}
