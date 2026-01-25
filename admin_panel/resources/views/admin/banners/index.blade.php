@extends('layouts.admin')

@section('header', 'Banners')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Banners</h1>
            <p class="text-sm text-slate-500 mt-1">Manage app home page banners</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Banners -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Banners</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-indigo-600 transition-colors">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-images text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Banners -->
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

        <!-- Inactive Banners -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Inactive</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-red-600 transition-colors">{{ number_format($stats['inactive']) }}</h3>
                </div>
                <div class="p-3 bg-red-50 rounded-xl text-red-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
            <div class="flex gap-2">
                <button id="delete-selected" style="display: none;" onclick="deleteSelected()" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-sm font-medium rounded-lg transition-all shadow-sm">
                    <i class="fas fa-trash mr-2"></i> Delete Selected
                </button>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i> Add Banner
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" id="select-all" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-4">Image</th>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($banners as $banner)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $banner->id }}" class="banner-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-16 w-32 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden relative group-hover:scale-105 transition-transform">
                                <a href="{{ \App\Helpers\FilePath::getUrl($banner->image) }}" target="_blank" class="block h-full w-full">
                                    <img src="{{ \App\Helpers\FilePath::getUrl($banner->image) }}" alt="Banner" class="h-full w-full object-cover">
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800">{{ $banner->title ?? 'No Title' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($banner->status)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
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
                                <p class="text-lg font-medium text-slate-700">No banners found</p>
                                <p class="text-sm text-slate-400 mt-1">Add banners to display on the app home screen.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.banner-checkbox');
        const deleteBtn = document.getElementById('delete-selected');

        function updateDeleteButton() {
            const checkedCount = document.querySelectorAll('.banner-checkbox:checked').length;
            if (checkedCount > 0) {
                deleteBtn.style.display = 'inline-flex';
            } else {
                deleteBtn.style.display = 'none';
            }
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
                updateDeleteButton();
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateDeleteButton();
                if(selectAll) {
                    const allChecked = document.querySelectorAll('.banner-checkbox:checked').length === checkboxes.length;
                    selectAll.checked = allChecked;
                }
            });
        });
    });

    function deleteSelected() {
        if (!confirm('Are you sure you want to delete selected banners?')) {
            return;
        }

        const selectedIds = Array.from(document.querySelectorAll('.banner-checkbox:checked')).map(cb => cb.value);

        fetch('{{ route("admin.banners.bulk_destroy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error deleting banners');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
</script>
@endsection