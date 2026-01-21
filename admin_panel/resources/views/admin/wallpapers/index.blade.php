@extends('layouts.admin')

@section('header', 'Wallpapers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Wallpapers</h1>
            <p class="text-sm text-slate-500 mt-1">Manage wallpapers collection</p>
        </div>
        <a href="{{ route('admin.wallpapers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all text-sm font-medium flex items-center gap-2 shadow-sm shadow-indigo-500/20">
            <i class="fas fa-plus"></i> Add New Wallpaper
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2 mb-4">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Wallpapers Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4">Image</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Views/Downloads</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($wallpapers as $wallpaper)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="h-16 w-24 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 relative group">
                                <img src="{{ asset('storage/' . $wallpaper->image) }}" alt="Wallpaper" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center transition-all">
                                    <i class="fas fa-image text-white"></i>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($wallpaper->category)
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $wallpaper->category->name }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs italic">Uncategorized</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <i class="fas fa-eye w-4 text-slate-400"></i>
                                    <span>{{ number_format($wallpaper->views) }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <i class="fas fa-download w-4 text-slate-400"></i>
                                    <span>{{ number_format($wallpaper->downloads) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wallpaper->status ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $wallpaper->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.wallpapers.edit', $wallpaper->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.wallpapers.destroy', $wallpaper->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this wallpaper?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                    <i class="fas fa-images text-3xl"></i>
                                </div>
                                <p class="text-lg font-medium text-slate-700">No wallpapers found</p>
                                <p class="text-sm text-slate-400 mt-1">Get started by adding your first wallpaper</p>
                                <a href="{{ route('admin.wallpapers.create') }}" class="mt-4 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">
                                    Add Wallpaper
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($wallpapers->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $wallpapers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
