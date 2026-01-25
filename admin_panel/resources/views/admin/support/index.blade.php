@extends('layouts.admin')

@section('header', 'Help Center')

@section('content')
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Support Tickets</h1>
            <p class="text-slate-500 text-sm mt-1">Manage and respond to user inquiries</p>
        </div>
        <a href="{{ route('admin.support.index') }}" class="p-2 bg-white text-slate-500 hover:text-indigo-600 rounded-lg border border-slate-200 shadow-sm transition-colors">
            <i class="fas fa-sync-alt"></i>
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Tickets -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Tickets</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-indigo-600 transition-colors">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-ticket-alt text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-indigo-600 font-medium bg-indigo-50 px-2 py-0.5 rounded mr-2">All Time</span>
                <span>Records</span>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(245,158,11,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Pending</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-amber-500 transition-colors">{{ number_format($stats['open']) }}</h3>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl text-amber-500 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded mr-2">Action Needed</span>
                <span>In Queue</span>
            </div>
        </div>

        <!-- Replied Tickets -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(59,130,246,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Replied</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-blue-600 transition-colors">{{ number_format($stats['replied']) }}</h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-reply text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-blue-600 font-medium bg-blue-50 px-2 py-0.5 rounded mr-2">Waiting User</span>
                <span>Response</span>
            </div>
        </div>

        <!-- Closed Tickets -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(16,185,129,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Resolved</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-emerald-600 transition-colors">{{ number_format($stats['closed']) }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-emerald-600 font-medium bg-emerald-50 px-2 py-0.5 rounded mr-2">Completed</span>
                <span>Successfully</span>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h2 class="font-bold text-slate-800">Recent Tickets</h2>
            <div class="flex gap-2">
                <!-- Add filters here if needed -->
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ticket Details</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Last Activity</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div class="h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold border border-indigo-100 overflow-hidden">
                                        @if($ticket->user && $ticket->user->avatar)
                                            <img src="{{ $ticket->user->avatar }}" alt="{{ $ticket->user->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr($ticket->user->name ?? 'U', 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 h-4 w-4 bg-white rounded-full flex items-center justify-center">
                                        <div class="h-2.5 w-2.5 rounded-full {{ $ticket->status == 'open' ? 'bg-amber-500' : ($ticket->status == 'replied' ? 'bg-blue-500' : 'bg-emerald-500') }}"></div>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors">#{{ $ticket->id }} - {{ $ticket->user->name ?? 'Unknown' }}</h3>
                                    <p class="text-xs text-slate-500">{{ $ticket->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-700 font-medium block max-w-[200px] truncate" title="{{ $ticket->subject }}">{{ $ticket->subject }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($ticket->status == 'open')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span> Open
                                </span>
                            @elseif($ticket->status == 'replied')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                    <i class="fas fa-reply mr-1.5 text-[10px]"></i> Replied
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <i class="fas fa-check mr-1.5 text-[10px]"></i> Closed
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($ticket->priority == 'high')
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-rose-50 text-rose-600 text-xs font-bold border border-rose-100">
                                    <i class="fas fa-flag"></i> High
                                </span>
                            @elseif($ticket->priority == 'medium')
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-amber-50 text-amber-600 text-xs font-bold border border-amber-100">
                                    <i class="fas fa-flag"></i> Medium
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                    <i class="fas fa-flag"></i> Low
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <i class="far fa-clock text-slate-400"></i>
                                {{ $ticket->updated_at->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="p-2 bg-white text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all border border-slate-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5" title="View Ticket">
                                    <i class="fas fa-comment-dots"></i>
                                </a>
                                <form action="{{ route('admin.support.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all border border-slate-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5" title="Delete Ticket">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-ticket-alt text-3xl text-slate-300"></i>
                                </div>
                                <h3 class="text-lg font-medium text-slate-600">No Tickets Found</h3>
                                <p class="text-sm mt-1">There are no support tickets at the moment.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
@endsection
