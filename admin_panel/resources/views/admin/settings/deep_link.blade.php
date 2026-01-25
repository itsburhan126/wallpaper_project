@extends('layouts.admin')

@section('header', 'Deep Link Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.deep_link.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Deep Link Configuration -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-link text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Deep Link App Settings</h3>
                        <p class="text-sm text-slate-500">Configure deep link scheme and app installation redirects</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Fallback Redirect URL (Play Store / App Store)</label>
                    <input type="url" name="deep_link_fallback_url" value="{{ $settings['deep_link_fallback_url'] ?? '' }}" 
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium text-slate-800"
                        placeholder="https://play.google.com/store/apps/details?id=com.example.game">
                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i> Users will be redirected here if the deep link fails or after a timeout.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">App Scheme (Deep Link Scheme)</label>
                    <input type="text" name="deep_link_app_scheme" value="{{ $settings['deep_link_app_scheme'] ?? 'bubblegame://' }}" 
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium text-slate-800">
                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle text-amber-500"></i> The custom scheme used to open your app (e.g., bubblegame://). Changing this requires an app update.
                    </p>
                    
                    <!-- Example Display -->
                    <div class="mt-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100/50">
                        <p class="text-xs text-indigo-600 font-bold mb-2 uppercase tracking-wider">Example Referral Link</p>
                        <code class="text-sm text-indigo-900 block bg-white/50 p-3 rounded-lg border border-indigo-100 font-mono">{{ $settings['deep_link_app_scheme'] ?? 'bubblegame://' }}refer?code=USER_ID</code>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">App Name (Display Name)</label>
                    <input type="text" name="deep_link_app_name" value="{{ $settings['deep_link_app_name'] ?? 'My Game' }}" 
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium text-slate-800">
                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                        <i class="fas fa-tag"></i> Name displayed on the fallback page.
                    </p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                <i class="fas fa-save text-lg"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
