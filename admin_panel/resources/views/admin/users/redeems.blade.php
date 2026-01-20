@extends('layouts.admin')

@section('header', 'Redeem History')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 transition-colors">Users</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('admin.users.show', $user->id) }}" class="hover:text-indigo-600 transition-colors">Profile</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-slate-800 font-medium">Redeems</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Redeem History</h1>
            <p class="text-sm text-slate-500 mt-1">For {{ $user->name }}</p>
        </div>
        <a href="{{ route('admin.users.show', $user->id) }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-indigo-600 hover:border-indigo-200 transition-all text-sm font-medium flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4">Method</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Cost</th>
                        <th class="px-6 py-4">Account Details</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($redeems as $req)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if(isset($req->method->gateway->icon))
                                    <img src="{{ asset($req->method->gateway->icon) }}" class="h-8 w-8 rounded-full object-contain bg-slate-50 border border-slate-100 p-1" alt="">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                                        <i class="fas fa-gift text-slate-400"></i>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-slate-700">{{ $req->method->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800">{{ $req->amount }} {{ $req->method->currency ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5 text-sm text-slate-600">
                                <i class="fas fa-coins text-amber-500 text-xs"></i>
                                {{ number_format($req->coin_cost) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-slate-100 px-2 py-1 rounded border border-slate-200 text-slate-600 font-mono select-all">{{ Str::limit($req->account_details, 25) }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                            @elseif($req->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Approved</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $req->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.users.show', ['id' => $user->id, 'request_id' => $req->id]) }}" class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700 hover:underline">
                                View Details <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                                    <i class="fas fa-inbox text-slate-300 text-xl"></i>
                                </div>
                                <p>No redeem requests found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($redeems->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $redeems->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
