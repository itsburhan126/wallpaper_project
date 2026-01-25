@extends('layouts.admin')

@section('header', 'Wallpaper Collection')

@section('content')
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Wallpapers</h1>
            <p class="text-slate-500 text-sm mt-1">Manage and curate your wallpaper gallery</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button type="button" onclick="toggleSelectAll()" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition-all text-sm font-medium flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-double text-slate-400"></i> <span class="hidden sm:inline">Select All</span>
            </button>
            <a href="{{ route('admin.wallpapers.create') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all text-sm font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5">
                <i class="fas fa-plus"></i> Add Wallpaper
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Wallpapers -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Items</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-indigo-600 transition-colors">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-image text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-indigo-600 font-medium bg-indigo-50 px-2 py-0.5 rounded mr-2">Gallery</span>
                <span>Count</span>
            </div>
        </div>

        <!-- Total Views -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(16,185,129,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Views</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-emerald-600 transition-colors">{{ number_format($stats['views']) }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-eye text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-emerald-600 font-medium bg-emerald-50 px-2 py-0.5 rounded mr-2">Global</span>
                <span>Impressions</span>
            </div>
        </div>

        <!-- Total Downloads -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(245,158,11,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Downloads</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-amber-500 transition-colors">{{ number_format($stats['downloads']) }}</h3>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl text-amber-500 group-hover:scale-110 transition-transform">
                    <i class="fas fa-download text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded mr-2">User</span>
                <span>Actions</span>
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(236,72,153,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Categories</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-pink-600 transition-colors">{{ number_format($stats['categories']) }}</h3>
                </div>
                <div class="p-3 bg-pink-50 rounded-xl text-pink-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-tags text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="text-pink-600 font-medium bg-pink-50 px-2 py-0.5 rounded mr-2">Active</span>
                <span>Collections</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 mb-8 shadow-sm animate-fade-in-down">
            <i class="fas fa-check-circle"></i>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.wallpapers.bulk_destroy') }}" method="POST" id="bulkDeleteForm">
        @csrf
        
        <!-- Floating Delete Button -->
        <div id="bulkDeleteAction" class="fixed bottom-8 right-8 z-50 transform translate-y-32 transition-transform duration-500 cubic-bezier(0.34, 1.56, 0.64, 1)">
            <button type="submit" onclick="return confirm('Are you sure you want to delete selected wallpapers?')" class="px-6 py-3 bg-rose-600 text-white rounded-full shadow-xl shadow-rose-500/30 hover:bg-rose-700 hover:scale-105 transition-all font-bold flex items-center gap-3 border-4 border-white">
                <i class="fas fa-trash-alt"></i>
                <span>Delete Selected (<span id="selectedCount">0</span>)</span>
            </button>
        </div>

        <!-- Wallpapers Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @forelse($wallpapers as $wallpaper)
            <div class="group relative bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 transform hover:-translate-y-1">
                <!-- Selection Checkbox -->
                <div class="absolute top-3 left-3 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <div class="bg-white rounded-lg p-1 shadow-sm">
                        <input type="checkbox" name="ids[]" value="{{ $wallpaper->id }}" class="wallpaper-checkbox w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer" onchange="updateBulkDeleteUI()">
                    </div>
                </div>

                <!-- Image Aspect Ratio Container -->
                <div class="aspect-[9/16] relative bg-slate-100 overflow-hidden">
                    <img src="{{ asset('storage/' . $wallpaper->image) }}" alt="Wallpaper" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    <!-- Overlay Actions -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-4">
                        <div class="flex justify-end transform -translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-white/20 text-white backdrop-blur-md border border-white/20">
                                {{ $wallpaper->category ? $wallpaper->category->name : 'Uncategorized' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-center gap-4 transform scale-90 group-hover:scale-100 transition-transform duration-300 delay-100">
                            <a href="{{ route('admin.wallpapers.edit', $wallpaper->id) }}" class="w-12 h-12 rounded-full bg-white text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-lg hover:shadow-indigo-500/50" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                        </div>
                        
                        <div class="flex justify-between items-end text-white text-xs font-medium transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">
                            <div class="flex items-center gap-1.5 bg-black/30 px-2 py-1 rounded-md backdrop-blur-sm">
                                <i class="fas fa-eye text-indigo-300"></i> {{ number_format($wallpaper->views) }}
                            </div>
                            <div class="flex items-center gap-1.5 bg-black/30 px-2 py-1 rounded-md backdrop-blur-sm">
                                <i class="fas fa-download text-emerald-300"></i> {{ number_format($wallpaper->downloads) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border-2 border-slate-200 border-dashed">
                <div class="flex flex-col items-center justify-center">
                    <div class="h-20 w-20 bg-indigo-50 rounded-full flex items-center justify-center mb-6 text-indigo-300 animate-bounce-slow">
                        <i class="fas fa-images text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">No wallpapers found</h3>
                    <p class="text-slate-400 mt-2 mb-6">Get started by adding your first wallpaper to the collection</p>
                    <a href="{{ route('admin.wallpapers.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all font-bold shadow-lg shadow-indigo-500/30">
                        Add New Wallpaper
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        @if($wallpapers->hasPages())
        <div class="mt-8 px-4 py-4 border-t border-slate-200">
            {{ $wallpapers->links() }}
        </div>
        @endif
    </form>
@endsection
