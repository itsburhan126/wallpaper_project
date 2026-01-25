@extends('layouts.admin')

@section('header', 'Games')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Games</h1>
            <p class="text-sm text-slate-500 mt-1">Manage game library and rewards</p>
        </div>
        <div class="flex gap-2">
            <button id="bulk-delete-btn" class="hidden px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all text-sm font-bold flex items-center gap-2 shadow-lg shadow-red-500/30 transform hover:-translate-y-0.5" onclick="bulkDelete()">
                <i class="fas fa-trash"></i> Delete Selected (<span id="selected-count">0</span>)
            </button>
            <a href="{{ route('admin.games.create') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all text-sm font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5">
                <i class="fas fa-plus"></i> Add New Game
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Games -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Games</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-indigo-600 transition-colors">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-gamepad text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Games -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Active</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-emerald-600 transition-colors">{{ number_format($stats['active']) }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Inactive Games -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Inactive</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-slate-600 transition-colors">{{ number_format($stats['inactive']) }}</h3>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl text-slate-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-pause-circle text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Featured Games -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Featured</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-amber-600 transition-colors">{{ number_format($stats['featured']) }}</h3>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl text-amber-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-star text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <i class="fas fa-check-circle"></i>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Games Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" id="select-all" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 shadow-sm transition-all duration-200 cursor-pointer w-4 h-4">
                        </th>
                        <th class="px-6 py-4">Image</th>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Rewards</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($games as $game)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="selected_games[]" value="{{ $game->id }}" class="game-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 shadow-sm transition-all duration-200 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-6 py-4">
                            <div class="relative">
                                @if($game->image)
                                <img src="{{ asset($game->image) }}" alt="{{ $game->title }}" class="h-12 w-12 object-cover rounded-lg shadow-sm border border-slate-100 group-hover:scale-105 transition-transform">
                                @else
                                <div class="h-12 w-12 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 border border-slate-200 group-hover:scale-105 transition-transform">
                                    <i class="fas fa-gamepad"></i>
                                </div>
                                @endif
                                @if($game->is_featured)
                                <div class="absolute -top-1 -right-1 bg-amber-400 text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full shadow-sm" title="Featured">
                                    <i class="fas fa-star"></i>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800">{{ $game->title }}</div>
                            <div class="text-xs text-slate-400 truncate max-w-[200px] mt-0.5">{{ $game->url }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-slate-600 flex items-center gap-1.5">
                                    <i class="fas fa-clock text-indigo-400"></i> {{ $game->play_time }}s
                                </span>
                                <span class="text-xs font-bold text-amber-600 flex items-center gap-1.5">
                                    <i class="fas fa-coins"></i> {{ number_format($game->win_reward) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($game->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.games.edit', $game->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all transform hover:scale-110" title="Edit Game">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this game?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all transform hover:scale-110" title="Delete Game">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-20 w-20 bg-indigo-50 rounded-full flex items-center justify-center mb-6 text-indigo-300 animate-bounce-slow">
                                    <i class="fas fa-gamepad text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800">No games found</h3>
                                <p class="text-slate-400 mt-2 mb-6">Get started by adding your first game to the library</p>
                                <a href="{{ route('admin.games.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all font-bold shadow-lg shadow-indigo-500/30">
                                    Add New Game
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($games->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $games->links() }}
        </div>
        @endif
    </div>
</div>
@endsection