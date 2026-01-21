@extends('layouts.admin')

@section('header', 'User Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 transition-colors">Users</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-slate-800 font-medium">Profile</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">User Profile</h1>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-indigo-600 hover:border-indigo-200 transition-all text-sm font-medium flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <!-- Current Redeem Request Action Card -->
    @if(isset($currentRequest) && $currentRequest->status == 'pending')
    <div class="bg-gradient-to-br from-indigo-50 to-white rounded-xl shadow-sm border border-indigo-100 p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5">
            <i class="fas fa-gift text-9xl text-indigo-600 transform rotate-12"></i>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-2.5 py-1 rounded-md bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider">
                    Action Required
                </span>
                <span class="text-sm text-slate-500">
                    Requested {{ $currentRequest->created_at->diffForHumans() }}
                </span>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">
                        {{ $currentRequest->amount }} {{ $currentRequest->method->currency }}
                    </h2>
                    <div class="flex items-center gap-3 text-slate-600 mb-4">
                        <div class="flex items-center gap-2">
                            @if($currentRequest->method->gateway->icon)
                                <img src="{{ asset($currentRequest->method->gateway->icon) }}" class="h-5 w-5 object-contain" alt="">
                            @endif
                            <span class="font-medium">{{ $currentRequest->method->name }}</span>
                        </div>
                        <span class="text-slate-300">|</span>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-coins text-amber-500"></i>
                            <span>Cost: {{ number_format($currentRequest->coin_cost) }} Coins</span>
                        </div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-sm rounded-lg p-3 border border-indigo-100 inline-block">
                        <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Account Details</p>
                        <code class="text-lg font-mono text-slate-800 select-all">{{ $currentRequest->account_details }}</code>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.redeem.requests.update', $currentRequest->id) }}" method="POST" onsubmit="return confirm('Reject this request?')">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="px-5 py-2.5 bg-white text-red-600 rounded-lg font-semibold hover:bg-red-50 transition-all border border-red-100 shadow-sm flex items-center gap-2">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </form>
                    <form action="{{ route('admin.redeem.requests.update', $currentRequest->id) }}" method="POST" onsubmit="return confirm('Approve this request?')">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg hover:shadow-indigo-500/20 flex items-center gap-2">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- User Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <!-- Avatar -->
            <div class="relative shrink-0">
                <div class="h-24 w-24 rounded-full bg-slate-100 flex items-center justify-center border-4 border-white shadow-sm ring-1 ring-slate-100">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-full w-full rounded-full object-cover">
                    @else
                        <span class="text-3xl font-bold text-slate-400">{{ substr($user->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="absolute bottom-1 right-1 h-5 w-5 bg-emerald-500 border-2 border-white rounded-full"></div>
            </div>

            <!-- Details -->
            <div class="flex-1 text-center md:text-left w-full">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h2>
                        <p class="text-slate-500 mb-3">{{ $user->email }}</p>
                        <div class="flex flex-wrap justify-center md:justify-start gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200">
                                ID: {{ $user->id }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full {{ $user->status === 'blocked' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} text-xs font-medium border">
                                {{ ucfirst($user->status) }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200">
                                Joined {{ $user->created_at->format('M d, Y') }}
                            </span>
                            @if($user->google_id)
                                <span class="px-2.5 py-0.5 rounded-full bg-orange-50 text-orange-600 text-xs font-medium border border-orange-100 flex items-center gap-1">
                                    <i class="fab fa-google"></i> Google Login
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        @php
                            $actionText = !$user->status ? 'unblock' : 'block';
                        @endphp

                        <form action="{{ route('admin.users.block', $user->id) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to {{ $actionText }} this user?')">

                            @csrf
                            @method('PUT')

                            <button type="submit"
                                class="px-4 py-2 rounded-lg font-medium transition-all shadow-sm flex items-center gap-2 border
                                {{ !$user->status
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100'
                                    : 'bg-red-50 text-red-700 border-red-100 hover:bg-red-100' }}">

                                <i class="fas {{ !$user->status ? 'fa-unlock' : 'fa-ban' }}"></i>
                                {{ !$user->status ? 'Unblock User' : 'Block User' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Coins -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500">
                    <i class="fas fa-coins text-lg"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">{{ number_format($user->coins) }}</h3>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mt-1">Total Coins</p>
        </div>

        <!-- Gems -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <div class="h-10 w-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-500">
                    <i class="fas fa-gem text-lg"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">{{ number_format($user->gems ?? 0) }}</h3>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mt-1">Total Gems</p>
        </div>

        <!-- Level -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                    <i class="fas fa-star text-lg"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">{{ $user->level ?? 1 }}</h3>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mt-1">Current Level</p>
        </div>

        <!-- Referrals -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <div class="h-10 w-10 rounded-lg bg-pink-50 flex items-center justify-center text-pink-500">
                    <i class="fas fa-users text-lg"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">{{ $user->referrals_count }}</h3>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mt-1">Total Referrals</p>
        </div>
    </div>

    <!-- Data Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-semibold text-slate-800">Recent Transactions</h3>
                <a href="{{ route('admin.users.transactions', $user->id) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $txn)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3">
                                <span class="font-medium text-slate-700 text-sm capitalize block">{{ str_replace('_', ' ', $txn->type) }}</span>
                                <span class="text-xs text-slate-400 truncate max-w-[150px] block">{{ $txn->description }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm font-semibold {{ $txn->amount > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $txn->amount > 0 ? '+' : '' }}{{ $txn->amount }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-xs text-slate-500">
                                {{ $txn->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">No recent transactions</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ad Watches -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-semibold text-slate-800">Recent Ad Watches</h3>
                <div class="text-xs text-slate-400">Latest activity</div>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                            <th class="px-6 py-3">Network</th>
                            <th class="px-6 py-3">Reward</th>
                            <th class="px-6 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($adWatches as $ad)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">
                                {{ $ad->provider ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-emerald-600 font-semibold">
                                +{{ $ad->reward_amount ?? 0 }}
                            </td>
                            <td class="px-6 py-3 text-xs text-slate-500">
                                {{ $ad->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">No ads watched yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Redeem Requests -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-semibold text-slate-800">Redeem History</h3>
                <a href="{{ route('admin.users.redeems', $user->id) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                            <th class="px-6 py-3">Method</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($redeemRequests as $req)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    @if(isset($req->method->gateway->icon))
                                        <img src="{{ asset($req->method->gateway->icon) }}" class="h-5 w-5 object-contain" alt="">
                                    @else
                                        <i class="fas fa-gift text-slate-400"></i>
                                    @endif
                                    <span class="text-sm font-medium text-slate-700">{{ $req->method->name ?? 'Unknown' }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5 ml-7">{{ $req->amount }} {{ $req->method->currency ?? '' }}</p>
                            </td>
                            <td class="px-6 py-3">
                                @if($req->status == 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                                @elseif($req->status == 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Approved</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 border border-red-100">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs text-slate-500">
                                {{ $req->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">No requests found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

         <!-- Referrals List -->
         <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-semibold text-slate-800">Recent Referrals</h3>
                <div class="text-xs text-slate-400">Latest joins</div>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($referrals as $ref)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs border border-indigo-100">
                                        {{ substr($ref->referredUser->name ?? 'U', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700">{{ $ref->referredUser->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-xs text-slate-500">
                                {{ $ref->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-slate-500 text-sm">No referrals yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
