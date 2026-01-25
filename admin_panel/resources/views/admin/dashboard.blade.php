@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Welcome back, {{ Auth::guard('admin')->user()->name }}!</h1>
        <p class="text-slate-500 text-sm mt-1">Here's what's happening with your app today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Users</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-indigo-600 transition-colors">{{ number_format($totalUsers) }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-emerald-600 font-medium bg-emerald-50 px-2 py-0.5 rounded mr-2">
                    <i class="fas fa-arrow-up mr-1"></i> {{ number_format($todayUsers) }}
                </span>
                <span>New Today</span>
            </div>
        </div>

        <!-- Total Wallpapers -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(236,72,153,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Wallpapers</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-pink-600 transition-colors">{{ number_format($totalWallpapers) }}</h3>
                </div>
                <div class="p-3 bg-pink-50 rounded-xl text-pink-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-image text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-pink-600 font-medium bg-pink-50 px-2 py-0.5 rounded mr-2">Active</span>
                <span>Collection</span>
            </div>
        </div>

        <!-- Pending Redeems -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(249,115,22,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Pending Requests</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-orange-600 transition-colors">{{ number_format($pendingRedeems) }}</h3>
                </div>
                <div class="p-3 bg-orange-50 rounded-xl text-orange-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-orange-600 font-medium bg-orange-50 px-2 py-0.5 rounded mr-2">Action</span>
                <span>Required</span>
            </div>
        </div>

        <!-- Total Payout -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(16,185,129,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Payout</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-emerald-600 transition-colors">${{ number_format($totalPayout, 2) }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-emerald-600 font-medium bg-emerald-50 px-2 py-0.5 rounded mr-2">Lifetime</span>
                <span>Distributed</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Users -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Top Earners</h3>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-700 text-xs font-bold uppercase tracking-wide flex items-center gap-1 hover:gap-2 transition-all">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Coins</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Level</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topUsers as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200 shadow-sm group-hover:scale-105 transition-transform">
                                            @if($user->avatar)
                                                <img src="{{ $user->avatar }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-sm font-bold text-slate-500">{{ substr($user->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-amber-500 bg-amber-50 px-2.5 py-1 rounded-lg text-xs border border-amber-100">
                                        <i class="fas fa-coins mr-1"></i> {{ number_format($user->coins) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">Lvl {{ $user->level }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-users mb-3 text-3xl text-slate-200"></i>
                                        <p>No users found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Redeems -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center text-violet-600">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Recent Requests</h3>
                </div>
                <a href="{{ route('admin.redeem.requests.index') }}" class="text-violet-600 hover:text-violet-700 text-xs font-bold uppercase tracking-wide flex items-center gap-1 hover:gap-2 transition-all">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Amount</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentRedeemRequests as $request)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                                            @if($request->user && $request->user->avatar)
                                                <img src="{{ $request->user->avatar }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-[10px] font-bold text-slate-500">{{ $request->user ? substr($request->user->name, 0, 1) : '?' }}</span>
                                            @endif
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700 truncate max-w-[100px]">{{ $request->user ? $request->user->name : 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded-md border border-slate-200">{{ $request->gateway_name }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-slate-800 text-sm">{{ $request->amount }} <span class="text-slate-400 text-xs">{{ $request->currency }}</span></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($request->status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span> PENDING
                                        </span>
                                    @elseif($request->status == 'approved' || $request->status == 'completed')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-check mr-1"></i> PAID
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                            <i class="fas fa-times mr-1"></i> {{ strtoupper($request->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox mb-3 text-3xl text-slate-200"></i>
                                        <p>No requests found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
