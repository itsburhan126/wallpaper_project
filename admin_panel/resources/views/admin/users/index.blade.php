@extends('layouts.admin')

@section('header', 'User Management')

@section('content')
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Users</h1>
            <p class="text-slate-500 text-sm mt-1">Manage and monitor all registered users</p>
        </div>
        <div class="flex items-center gap-4 w-full sm:w-auto">
            <form action="{{ route('admin.users.index') }}" method="GET" class="w-full sm:w-auto">
                <div class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full sm:w-72 pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-700 placeholder-slate-400 shadow-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                </div>
            </form>
            <!-- Add User Button could go here -->
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">User Details</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Stats</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold border border-slate-200 shadow-sm overflow-hidden">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                    @else
                                        {{ substr($user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</h3>
                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->status === 'blocked')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                    Blocked
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 rounded bg-amber-50 text-amber-500">
                                    <i class="fas fa-coins text-xs"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">{{ number_format($user->coins) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-slate-600">
                                    <i class="fas fa-users text-slate-400 mr-1 w-4"></i> {{ $user->referrals_count }} Refs
                                </span>
                                <span class="text-xs text-slate-600">
                                    <i class="fas fa-receipt text-slate-400 mr-1 w-4"></i> {{ $user->redeem_requests_count }} Redeems
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="p-2 bg-white text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-slate-200 shadow-sm" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-slate-200 shadow-sm" title="Delete User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-4xl mb-3 text-slate-200"></i>
                                <p class="text-slate-500 font-medium">No users found</p>
                                <p class="text-slate-400 text-xs mt-1">Try adjusting your search query</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
@endsection
