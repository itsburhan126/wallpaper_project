@extends('layouts.admin')

@section('header', 'Transaction History')

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
                <span class="text-slate-800 font-medium">Transactions</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Transaction History</h1>
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
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $txn)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800 text-sm capitalize bg-slate-100 px-2 py-1 rounded border border-slate-200">
                                {{ str_replace('_', ' ', $txn->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $txn->description }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold {{ $txn->amount > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $txn->amount > 0 ? '+' : '' }}{{ $txn->amount }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $txn->created_at->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 text-sm">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                                    <i class="fas fa-list text-slate-300 text-xl"></i>
                                </div>
                                <p>No transactions found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
