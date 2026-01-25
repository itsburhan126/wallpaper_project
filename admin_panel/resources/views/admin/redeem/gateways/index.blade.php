@extends('layouts.admin')

@section('header', 'Redeem Gateways')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Payment Gateways</h1>
            <p class="text-sm text-slate-500 mt-1">Manage redeem gateways and their withdrawal methods</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Gateways -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Gateways</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-indigo-600 transition-colors">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Gateways -->
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

        <!-- Inactive Gateways -->
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

    <!-- Create New Gateway -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-plus-circle text-indigo-500"></i> Add New Gateway
            </h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.redeem.gateways.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Gateway Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" placeholder="e.g. PayPal" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Priority</label>
                        <input type="number" name="priority" value="0" min="0" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Icon <span class="text-red-500">*</span></label>
                        <input type="file" name="icon" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all" required>
                    </div>

                    <div class="lg:col-span-4 flex items-center justify-between pt-4 border-t border-slate-100 mt-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500" checked>
                            <label for="is_active" class="ml-2 block text-sm font-medium text-slate-700 cursor-pointer select-none">
                                Active (Show in app)
                            </label>
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-sm flex items-center gap-2">
                            <i class="fas fa-save"></i> Add Gateway
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Gateways List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-bold text-slate-800">Active Gateways</h3>
            <button type="button" onclick="deleteSelected()" id="deleteSelectedBtn" class="hidden bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-4 py-2 rounded-lg text-sm font-medium transition-all items-center gap-2">
                <i class="fas fa-trash-alt"></i> Delete Selected
            </button>
        </div>
        <div class="overflow-x-auto">
            <form id="bulkDeleteForm" action="{{ route('admin.redeem.gateways.bulk_destroy') }}" method="POST">
                @csrf
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                            <th class="px-6 py-4 w-4">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-4">Icon</th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Priority</th>
                            <th class="px-6 py-4">Methods</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($gateways as $gateway)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $gateway->id }}" class="gateway-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-4">
                                @if($gateway->icon)
                                    <img src="/{{ $gateway->icon }}?v={{ time() }}" alt="{{ $gateway->name }}" class="w-10 h-10 rounded-lg object-contain bg-slate-50 border border-slate-200 p-1">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center border border-slate-200">
                                        <i class="fas fa-image text-slate-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $gateway->name }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded text-xs font-mono">{{ $gateway->priority }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $gateway->methods_count }} Methods
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gateway->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $gateway->is_active ? 'bg-emerald-500' : 'bg-red-500' }} mr-1.5"></span>
                                    {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.redeem.methods.index', $gateway->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Manage Methods">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    
                                    <button type="button" onclick="confirmDelete('{{ route('admin.redeem.gateways.destroy', $gateway->id) }}')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Gateway">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                        <i class="fas fa-wallet text-3xl"></i>
                                    </div>
                                    <p class="text-lg font-medium text-slate-700">No gateways found</p>
                                    <p class="text-sm text-slate-400 mt-1">Add your first payment gateway above</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<form id="deleteForm" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.gateway-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    function updateDeleteButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (anyChecked) {
            deleteSelectedBtn.classList.remove('hidden');
            deleteSelectedBtn.classList.add('flex');
        } else {
            deleteSelectedBtn.classList.add('hidden');
            deleteSelectedBtn.classList.remove('flex');
        }
    }

    if(selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateDeleteButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteButton);
    });

    function deleteSelected() {
        if (confirm('Are you sure you want to delete the selected gateways? All associated methods will also be deleted.')) {
            bulkDeleteForm.submit();
        }
    }

    function confirmDelete(url) {
        if (confirm('Are you sure? All associated methods will also be deleted.')) {
            const form = document.getElementById('deleteForm');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection