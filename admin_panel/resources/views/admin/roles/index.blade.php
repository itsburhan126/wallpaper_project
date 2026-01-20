@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manage Roles</h1>
            <p class="text-slate-500 text-sm mt-1">Control user access and permissions</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shadow-indigo-200">
            <i class="fas fa-plus mr-2"></i> Create New Role
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="h-12 w-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100">
                    <i class="fas fa-shield-alt text-xl"></i>
                </div>
                <div class="flex gap-2">
                    @if($role->slug !== 'super-admin')
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit Role">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Delete Role">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            
            <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $role->name }}</h3>
            <p class="text-sm text-slate-500 mb-4">{{ $role->admins_count }} Staff Members</p>
            
            <div class="border-t border-slate-100 pt-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Permissions</p>
                <div class="flex flex-wrap gap-2">
                    @if($role->slug === 'super-admin')
                        <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-xs rounded-md font-medium border border-emerald-100">All Permissions</span>
                    @else
                        @forelse(array_slice($role->permissions ?? [], 0, 3) as $permission)
                            <span class="px-2 py-1 bg-slate-50 text-slate-600 text-xs rounded-md font-medium border border-slate-100">{{ str_replace('_', ' ', Str::title($permission)) }}</span>
                        @empty
                            <span class="text-xs text-slate-400 italic">No permissions assigned</span>
                        @endforelse
                        @if(count($role->permissions ?? []) > 3)
                            <span class="px-2 py-1 bg-slate-50 text-slate-400 text-xs rounded-md font-medium border border-slate-100">+{{ count($role->permissions) - 3 }} more</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
