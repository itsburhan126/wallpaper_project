@extends('layouts.admin')

@section('header', 'Banners')

@section('content')
<div class="container-fluid">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Banners</h1>
            <p class="text-slate-500 text-sm mt-1">Manage app home page banners</p>
        </div>
        <div class="flex gap-2">
            <button id="delete-selected" style="display: none;" onclick="deleteSelected()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shadow-rose-200">
                <i class="fas fa-trash mr-2"></i> Delete Selected
            </button>
            <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shadow-indigo-200">
                <i class="fas fa-plus mr-2"></i> Add Banner
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider w-10">
                            <input type="checkbox" id="select-all" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Image</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($banners as $banner)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="ids[]" value="{{ $banner->id }}" class="banner-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="h-16 w-32 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden relative group-hover:shadow-md transition-all">
                                <a href="{{ \App\Helpers\FilePath::getUrl($banner->image) }}" target="_blank" class="block h-full w-full">
                                    <img src="{{ \App\Helpers\FilePath::getUrl($banner->image) }}" alt="Banner" class="h-full w-full object-cover">
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900">{{ $banner->title ?? 'No Title' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($banner->status)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-slate-500 rounded-full"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
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
                                <a href="{{ route('admin.banners.create') }}" class="mt-4 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-sm font-medium">
                                    Add Banner
                                </a>
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
