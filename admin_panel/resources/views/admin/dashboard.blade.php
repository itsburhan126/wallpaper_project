@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Total Users</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($totalUsers) }}</h3>
                    <div class="mt-2 flex items-center text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md w-fit">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>Active Base</span>
                    </div>
                </div>
                <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Users -->
        <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-sm font-medium">New Users Today</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($todayUsers) }}</h3>
                    <div class="mt-2 flex items-center text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md w-fit">
                        <i class="fas fa-plus mr-1"></i>
                        <span>Growth</span>
                    </div>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600">
                    <i class="fas fa-user-plus text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Redeems -->
        <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Total Redeems</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($totalRedeemRequests) }}</h3>
                     <div class="mt-2 flex items-center text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-md w-fit">
                        <span>Lifetime</span>
                    </div>
                </div>
                <div class="p-3 bg-violet-50 rounded-lg text-violet-600">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Today Redeems -->
        <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Redeems Today</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($todayRedeemRequests) }}</h3>
                    <div class="mt-2 flex items-center text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded-md w-fit">
                        <i class="fas fa-clock mr-1"></i>
                        <span>Processing</span>
                    </div>
                </div>
                <div class="p-3 bg-orange-50 rounded-lg text-orange-600">
                    <i class="fas fa-history text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Users -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">Top 10 Coin Earners</h3>
                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold uppercase tracking-wide">View All</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Coins</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Level</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topUsers as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                                            @if($user->avatar)
                                                <img src="{{ $user->avatar }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xs font-bold text-slate-500">{{ substr($user->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-amber-500 bg-amber-50 px-2 py-1 rounded-md text-xs">{{ number_format($user->coins) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Lvl {{ $user->level }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fas fa-users mb-2 text-2xl text-slate-300"></i>
                                    <p>No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Redeems -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">Recent Redeem Requests</h3>
                <a href="{{ route('admin.redeem.requests.index') }}" class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold uppercase tracking-wide">View All</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Amount</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentRedeemRequests as $request)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                                            @if($request->user && $request->user->avatar)
                                                <img src="{{ $request->user->avatar }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-[10px] font-bold text-slate-500">{{ $request->user ? substr($request->user->name, 0, 1) : '?' }}</span>
                                            @endif
                                        </div>
                                        <span class="text-sm font-medium text-slate-700 truncate max-w-[100px]">{{ $request->user ? $request->user->name : 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded-md">{{ $request->gateway_name }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-slate-800 text-sm">{{ $request->amount }} <span class="text-slate-400 text-xs">{{ $request->currency }}</span></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($request->status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">PENDING</span>
                                    @elseif($request->status == 'approved' || $request->status == 'completed')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">PAID</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">{{ strtoupper($request->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fas fa-inbox mb-2 text-2xl text-slate-300"></i>
                                    <p>No requests found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
