@extends('layouts.admin')

@section('header', 'Wallpapers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Wallpapers</h1>
            <p class="text-sm text-slate-500 mt-1">Manage wallpaper collection</p>
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
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @forelse($wallpapers as $wallpaper)
        <div class="group bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all">
            <div class="relative aspect-[9/16] bg-slate-100 overflow-hidden">
                <img src="{{ asset('storage/' . $wallpaper->thumbnail) }}" alt="Wallpaper" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.wallpapers.edit', $wallpaper->id) }}" class="p-2 bg-white/20 backdrop-blur-md text-white rounded-lg hover:bg-white/40 transition-colors">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.wallpapers.destroy', $wallpaper->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this wallpaper?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-white/20 backdrop-blur-md text-white rounded-lg hover:bg-red-500/80 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="absolute top-2 right-2">
                     <span class="px-2 py-1 rounded-md text-xs font-bold bg-white/80 backdrop-blur-sm text-slate-800 shadow-sm">
                        {{ $wallpaper->category->name }}
                    </span>
                </div>
            </div>
            <div class="p-3">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-eye"></i> {{ $wallpaper->views }}
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fas fa-download"></i> {{ $wallpaper->downloads }}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-500 bg-white rounded-xl border border-slate-200 border-dashed">
            <div class="flex flex-col items-center justify-center">
                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-3">
                    <i class="fas fa-images text-2xl"></i>
                </div>
                <p class="font-medium text-slate-600">No wallpapers found</p>
                <p class="text-sm mt-1">Start by adding your first wallpaper</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $wallpapers->links() }}
    </div>
</div>
@endsection
