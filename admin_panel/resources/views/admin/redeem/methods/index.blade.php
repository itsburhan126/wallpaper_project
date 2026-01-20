@extends('layouts.admin')

@section('header', 'Redeem Methods - ' . $gateway->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.redeem.gateways.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Methods: {{ $gateway->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1">Manage withdrawal methods for {{ $gateway->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Create New Method -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-plus-circle text-indigo-500"></i> Add New Method
            </h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.redeem.methods.store', $gateway->id) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Method Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" placeholder="e.g. 500 BDT" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm" required>
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Input Hint <span class="text-red-500">*</span></label>
                        <input type="text" name="input_hint" placeholder="e.g. Enter Bkash Number" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm" required>
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Coin Cost <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-coins absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="number" name="coin_cost" placeholder="e.g. 5000" min="1" class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm" required>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Amount <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" placeholder="e.g. 500" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm" required>
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Currency <span class="text-red-500">*</span></label>
                        <input type="text" name="currency" placeholder="e.g. BDT" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm" required>
                    </div>

                    <div class="lg:col-span-3 flex items-center justify-between pt-4 border-t border-slate-100 mt-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500" checked>
                            <label for="is_active" class="ml-2 block text-sm font-medium text-slate-700 cursor-pointer select-none">
                                Active (Show in app)
                            </label>
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-sm flex items-center gap-2">
                            <i class="fas fa-save"></i> Add Method
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Methods List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-bold text-slate-800">Active Methods</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-semibold text-slate-500 border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Input Hint</th>
                        <th class="px-6 py-4">Coin Cost</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Currency</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($methods as $method)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4 font-bold text-slate-700">{{ $method->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $method->input_hint }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5 font-mono text-amber-600 font-bold text-sm">
                                <i class="fas fa-coins text-amber-500 text-xs"></i>
                                {{ number_format($method->coin_cost) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-emerald-600 font-bold text-sm">{{ $method->amount }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $method->currency }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $method->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $method->is_active ? 'bg-emerald-500' : 'bg-red-500' }} mr-1.5"></span>
                                {{ $method->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('admin.redeem.methods.destroy', $method->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this method?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Method">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                    <i class="fas fa-list-ul text-3xl"></i>
                                </div>
                                <p class="text-lg font-medium text-slate-700">No methods found</p>
                                <p class="text-sm text-slate-400 mt-1">Add your first withdrawal method above</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
