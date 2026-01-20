@extends('layouts.admin')

@section('header', 'Staff Management')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4 w-full md:w-auto flex-1">
            <h2 class="text-xl font-bold text-gray-800">Manage Staff Members</h2>
        </div>

        <a href="{{ route('admin.staff.create') }}" 
           class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all duration-200 font-medium">
            <i class="fas fa-user-plus"></i>
            <span>Add New Staff</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="px-6 py-4">Staff Member</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Joined Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($staff as $member)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($member->role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                    {{ $member->role->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                    No Role
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $member->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.staff.edit', $member->id) }}" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors bg-white hover:bg-indigo-50 rounded-lg">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors bg-white hover:bg-red-50 rounded-lg">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-900">No staff members found</p>
                                <p class="text-sm text-gray-500">Get started by adding a new staff member.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
