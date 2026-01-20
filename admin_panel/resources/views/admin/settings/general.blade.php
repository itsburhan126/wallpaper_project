@extends('layouts.admin')

@section('header', 'General Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- General Information -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">General Information</h3>
                        <p class="text-sm text-slate-500">Basic configuration for your application</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">App Name</label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400" placeholder="Your App Name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '$' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Support Email</label>
                    <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}" 
                        class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400"
                        placeholder="support@example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sender Email (For Emails)</label>
                    <input type="email" name="sender_email" value="{{ $settings['sender_email'] ?? '' }}" 
                        class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400"
                        placeholder="noreply@example.com">
                    <p class="text-xs text-slate-500 mt-1">This email will be used as the "From" address for system emails.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Privacy Policy URL</label>
                    <input type="url" name="privacy_policy_url" value="{{ $settings['privacy_policy_url'] ?? '' }}" 
                        class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400"
                        placeholder="https://example.com/privacy">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Terms of Service URL</label>
                    <input type="url" name="terms_url" value="{{ $settings['terms_url'] ?? '' }}" 
                        class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400"
                        placeholder="https://example.com/terms">
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-address-book"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Contact Information</h3>
                        <p class="text-sm text-slate-500">How users can reach you</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" 
                        class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Contact Address</label>
                    <textarea name="contact_address" rows="3" 
                        class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">{{ $settings['contact_address'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
