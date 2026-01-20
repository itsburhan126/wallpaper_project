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
        <a href="{{ route('admin.games.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all text-sm font-medium flex items-center gap-2 shadow-sm shadow-indigo-500/20">
            <i class="fas fa-plus"></i> Add New Game
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2 mb-4">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Games Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4">Image</th>
                        <th class="px-6 py-4">Title</th>

        
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($games as $game)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            @if($game->image)
                            <img src="{{ asset($game->image) }}" alt="{{ $game->title }}" class="h-12 w-12 object-cover rounded-lg shadow-sm border border-slate-100">
                            @else
                            <div class="h-12 w-12 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 border border-slate-200">
                                <i class="fas fa-gamepad"></i>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800">{{ $game->title }}</div>
                            <div class="text-xs text-slate-400 truncate max-w-[200px] mt-0.5">{{ $game->url }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $game->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $game->is_active ? 'bg-emerald-500' : 'bg-red-500' }} mr-1.5"></span>
                                {{ $game->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.games.edit', $game->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit Game">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this game?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Game">
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
                                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                    <i class="fas fa-gamepad text-3xl"></i>
                                </div>
                                <p class="text-lg font-medium text-slate-700">No games found</p>
                                <p class="text-sm text-slate-400 mt-1">Get started by adding your first game</p>
                                <a href="{{ route('admin.games.create') }}" class="mt-4 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">
                                    Add Game
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
