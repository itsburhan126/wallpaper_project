@extends('layouts.admin')

@section('header', 'General Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">General Settings</h1>
            <p class="text-slate-500 text-sm mt-1">Basic configuration for your application</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- General Information -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-sliders-h text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">General Information</h3>
                        <p class="text-sm text-slate-500">App name, currency, and other basics</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">App Name</label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium" placeholder="Your App Name">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '$' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Coin Conversion Rate</label>
                        <div class="relative">
                            <i class="fas fa-exchange-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="number" name="coin_rate" value="{{ $settings['coin_rate'] ?? '1000' }}" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium" placeholder="e.g. 1000">
                        </div>
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle text-indigo-400"></i>
                            How many coins equal 1 currency unit (e.g., 1000 coins = $1)
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Wallpaper View Time (Seconds)</label>
                        <div class="relative">
                            <i class="fas fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="number" name="wallpaper_view_time" value="{{ $settings['wallpaper_view_time'] ?? '10' }}" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium" placeholder="e.g. 15">
                        </div>
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle text-indigo-400"></i>
                            Time required to view wallpaper before claiming coins
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Support Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium"
                                placeholder="support@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Sender Email (System)</label>
                        <div class="relative">
                            <i class="fas fa-paper-plane absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="email" name="sender_email" value="{{ $settings['sender_email'] ?? '' }}" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium"
                                placeholder="noreply@example.com">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Privacy Policy URL</label>
                        <div class="relative">
                            <i class="fas fa-link absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="url" name="privacy_policy_url" value="{{ $settings['privacy_policy_url'] ?? '' }}" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium"
                                placeholder="https://example.com/privacy">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Terms of Service URL</label>
                        <div class="relative">
                            <i class="fas fa-link absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="url" name="terms_url" value="{{ $settings['terms_url'] ?? '' }}" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium"
                                placeholder="https://example.com/terms">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm">
                        <i class="fas fa-address-book text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Contact Information</h3>
                        <p class="text-sm text-slate-500">How users can reach you</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Contact Phone</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder-slate-400 font-medium">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Contact Address</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-4 top-4 text-slate-400"></i>
                        <textarea name="contact_address" rows="3" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder-slate-400 font-medium">{{ $settings['contact_address'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                <i class="fas fa-save text-lg"></i>
                <span>Save All Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
