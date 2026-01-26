@extends('layouts.admin')

@section('header', 'Shorts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Shorts Videos</h1>
            <p class="text-sm text-slate-500 mt-1">Manage short videos content</p>
        </div>
        <a href="{{ route('admin.shorts.create') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all text-sm font-bold flex items-center gap-2 shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i> Add New Short
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Shorts -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Shorts</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-indigo-600 transition-colors">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-video text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Shorts -->
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

        <!-- Total Views -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Views</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-amber-600 transition-colors">{{ number_format($stats['views']) }}</h3>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl text-amber-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-eye text-xl"></i>
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

    <!-- Shorts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4">Thumbnail</th>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Video URL</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shorts as $short)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            @if($short->thumbnail_url)
                            <img src="{{ asset($short->thumbnail_url) }}" alt="{{ $short->title }}" class="h-16 w-10 object-cover rounded-lg shadow-sm border border-slate-100 group-hover:scale-105 transition-transform">
                            @else
                            <div class="h-16 w-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 border border-slate-200 group-hover:scale-105 transition-transform">
                                <i class="fas fa-video"></i>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800">{{ $short->title }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">Created: {{ $short->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ asset($short->video_url) }}" target="_blank" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2.5 py-1 rounded-md transition-colors">
                                <i class="fas fa-external-link-alt mr-1"></i> View Video
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1 text-sm font-medium text-slate-600">
                                <i class="fas fa-eye text-slate-400 text-xs"></i>
                                {{ number_format($short->views) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($short->is_active)
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
                                <form action="{{ route('admin.shorts.destroy', $short->id) }}" method="POST" class="inline-block" onsubmit="return confirm('আপনি কি নিশ্চিত যে আপনি এই শর্টটি মুছতে চান?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all transform hover:scale-110" title="মুছুন">
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
                                    <i class="fas fa-video text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800">No shorts found</h3>
                                <p class="text-slate-400 mt-2 mb-6">Get started by adding your first short video</p>
                                <a href="{{ route('admin.shorts.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all font-bold shadow-lg shadow-indigo-500/30">
                                    Add New Short
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shorts->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $shorts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection