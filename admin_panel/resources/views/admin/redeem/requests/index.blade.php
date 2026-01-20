@extends('layouts.admin')

@section('header', 'Redeem Requests')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Redeem Requests</h1>
            <p class="text-sm text-slate-500 mt-1">Manage and process user withdrawal requests</p>
        </div>
        
        <!-- Filter/Search Placeholder (can be expanded later) -->
        <div class="flex items-center gap-2">
            <div class="relative group">
                <input type="text" placeholder="Search requests..." class="pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-700 placeholder-slate-400 shadow-sm w-full sm:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Method</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Account Details</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $request)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs border border-indigo-100">
                                    {{ substr($request->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $request->user->name ?? 'Unknown User' }}</p>
                                    <p class="text-xs text-slate-500">{{ $request->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if(isset($request->method->gateway->icon))
                                    <img src="{{ asset($request->method->gateway->icon) }}" class="h-6 w-6 object-contain" alt="">
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-slate-700">{{ $request->method->name ?? $request->gateway_name }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wide">{{ $request->method->gateway->name ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800">{{ $request->amount }} {{ $request->currency }}</span>
                                <span class="text-xs text-amber-600 font-medium flex items-center gap-1">
                                    <i class="fas fa-coins text-[10px]"></i> {{ number_format($request->coin_cost) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-slate-100 px-2 py-1 rounded border border-slate-200 text-slate-600 font-mono select-all">{{ Str::limit($request->account_details, 20) }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @if($request->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Approved
                                </span>
                            @elseif($request->status == 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $request->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', ['id' => $request->user_id, 'request_id' => $request->id]) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($request->status == 'pending')
                                <div class="w-px h-4 bg-slate-200 mx-1"></div>
                                
                                <form action="{{ route('admin.redeem.requests.update', $request->id) }}" method="POST" onsubmit="return confirm('Approve this request?')">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.redeem.requests.update', $request->id) }}" method="POST" onsubmit="return confirm('Reject this request?')">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                    <i class="fas fa-inbox text-3xl"></i>
                                </div>
                                <p class="text-lg font-medium text-slate-700">No redeem requests found</p>
                                <p class="text-sm text-slate-400 mt-1">Requests will appear here when users redeem rewards</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
