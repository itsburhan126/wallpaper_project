@extends('layouts.admin')

@section('header', 'Ticket Details')

@section('content')
    <div class="max-w-6xl mx-auto h-[calc(100vh-8rem)] flex flex-col">
        <!-- Header / Back Navigation -->
        <div class="mb-6 flex items-center justify-between shrink-0">
            <a href="{{ route('admin.support.index') }}" class="group flex items-center text-slate-500 hover:text-indigo-600 transition-colors">
                <div class="h-8 w-8 rounded-full bg-white border border-slate-200 flex items-center justify-center mr-3 group-hover:border-indigo-200 group-hover:bg-indigo-50 transition-all shadow-sm">
                    <i class="fas fa-arrow-left text-xs"></i>
                </div>
                <span class="font-medium">Back to Tickets</span>
            </a>
            
            <div class="flex items-center gap-3">
                <div class="px-3 py-1 rounded-full text-xs font-bold border {{ $ticket->priority == 'high' ? 'bg-rose-50 text-rose-600 border-rose-100' : ($ticket->priority == 'medium' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-50 text-slate-600 border-slate-100') }}">
                    Priority: {{ ucfirst($ticket->priority) }}
                </div>
                <div class="px-3 py-1 rounded-full text-xs font-bold border {{ $ticket->status == 'open' ? 'bg-amber-50 text-amber-600 border-amber-100' : ($ticket->status == 'replied' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100') }}">
                    Status: {{ ucfirst($ticket->status) }}
                </div>
            </div>
        </div>

        <!-- Main Chat Container -->
        <div class="flex-1 bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden flex flex-col md:flex-row">
            
            <!-- Left Side: Ticket Info (Collapsible on mobile?) -->
            <div class="w-full md:w-80 bg-slate-50/50 border-r border-slate-200 flex flex-col shrink-0">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Ticket Information</h3>
                    <h2 class="text-lg font-bold text-slate-800 leading-tight mb-2">{{ $ticket->subject }}</h2>
                    <p class="text-xs text-slate-500">Created {{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                </div>
                
                <div class="p-6 flex-1 overflow-y-auto">
                    <div class="mb-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">User Details</h3>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 overflow-hidden shadow-sm">
                                @if($ticket->user && $ticket->user->avatar)
                                    <img src="{{ $ticket->user->avatar }}" alt="{{ $ticket->user->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ substr($ticket->user->name ?? 'U', 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $ticket->user->name ?? 'Unknown User' }}</p>
                                <p class="text-xs text-slate-500">{{ $ticket->user->email ?? '' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <div class="bg-white p-3 rounded-lg border border-slate-100 text-center">
                                <span class="block text-xs text-slate-400">Total Tickets</span>
                                <span class="block font-bold text-slate-700">12</span> <!-- Placeholder or need relationship -->
                            </div>
                            <div class="bg-white p-3 rounded-lg border border-slate-100 text-center">
                                <span class="block text-xs text-slate-400">Balance</span>
                                <span class="block font-bold text-slate-700">{{ number_format($ticket->user->coins ?? 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Actions</h3>
                        <form action="{{ route('admin.support.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?')" class="block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2.5 px-4 bg-white border border-rose-200 text-rose-600 rounded-xl hover:bg-rose-50 transition-colors text-sm font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt"></i> Delete Ticket
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side: Chat Area -->
            <div class="flex-1 flex flex-col bg-slate-50/30">
                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6 scroll-smooth" id="chat-messages">
                    @foreach($ticket->messages as $message)
                        @if($message->sender_type == 'admin')
                            <!-- Admin Message (Right) -->
                            <div class="flex justify-end pl-12">
                                <div class="flex flex-col items-end gap-1 max-w-xl">
                                    <div class="bg-gradient-to-br from-indigo-600 to-violet-600 text-white rounded-2xl rounded-tr-none px-5 py-3 shadow-md">
                                        <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>
                                    </div>
                                    <span class="text-[10px] font-medium text-slate-400 pr-1">{{ $message->created_at->format('h:i A') }} • You</span>
                                </div>
                            </div>
                        @else
                            <!-- User Message (Left) -->
                            <div class="flex justify-start pr-12">
                                <div class="flex items-end gap-3 max-w-xl">
                                    <div class="h-8 w-8 rounded-full bg-slate-200 shrink-0 border border-slate-300 overflow-hidden mb-1">
                                        @if($ticket->user && $ticket->user->avatar)
                                            <img src="{{ $ticket->user->avatar }}" alt="{{ $ticket->user->name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="flex h-full w-full items-center justify-center text-xs font-bold text-slate-500">{{ substr($ticket->user->name ?? 'U', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <div class="bg-white border border-slate-100 text-slate-700 rounded-2xl rounded-tl-none px-5 py-3 shadow-sm">
                                            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>
                                        </div>
                                        <span class="text-[10px] font-medium text-slate-400 pl-1">{{ $message->created_at->format('h:i A') }} • {{ $ticket->user->name }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Reply Area -->
                <div class="p-4 bg-white border-t border-slate-200">
                    <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST" class="relative">
                        @csrf
                        <div class="relative">
                            <textarea name="message" rows="3" placeholder="Type your reply here..." class="w-full pl-4 pr-14 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-500/20 resize-none text-sm transition-all" required></textarea>
                            <div class="absolute bottom-3 right-3">
                                <button type="submit" class="h-10 w-10 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center justify-center shadow-md transition-all hover:scale-105 active:scale-95">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-2 ml-1">
                            <i class="fas fa-info-circle mr-1"></i> Pressing send will notify the user via email/push.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
