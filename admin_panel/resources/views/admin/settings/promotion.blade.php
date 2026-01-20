@extends('layouts.admin')

@section('header', 'Promotion Pricing & Payment')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.promotion.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Promotion Pricing & Payment Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Promotion Pricing & Payment</h3>
                        <p class="text-sm text-gray-500">Configure costs for user promotions and payment details</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cost Per Click (CPC) ($)</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="ad_cpc_price" value="{{ $settings['ad_cpc_price'] ?? '5.00' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all pl-10">
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Amount deducted from budget per click</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cost Per 1000 Impressions (CPM) ($)</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="ad_cpm_price" value="{{ $settings['ad_cpm_price'] ?? '20.00' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all pl-10">
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Amount deducted from budget per 1000 views</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Instructions</label>
                        <textarea name="ad_payment_info" rows="5" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all"
                            placeholder="Enter bank details, mobile money numbers, etc.">{{ $settings['ad_payment_info'] ?? '' }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Displayed to users when they request a promotion</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-green-600 to-teal-600 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Save Promotion Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
