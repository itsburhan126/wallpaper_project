@extends('layouts.admin')

@section('header', 'Deep Link Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.deep_link.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Deep Link Configuration -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                        <i class="fas fa-link"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Deep Link App Settings</h3>
                        <p class="text-sm text-gray-500">Configure deep link scheme and app installation redirects</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fallback Redirect URL (Play Store / App Store)</label>
                    <input type="url" name="deep_link_fallback_url" value="{{ $settings['deep_link_fallback_url'] ?? '' }}" 
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400"
                        placeholder="https://play.google.com/store/apps/details?id=com.example.game">
                    <p class="text-xs text-gray-500 mt-1">Users will be redirected here if the deep link fails or after a timeout.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">App Scheme (Deep Link Scheme)</label>
                    <input type="text" name="deep_link_app_scheme" value="{{ $settings['deep_link_app_scheme'] ?? 'bubblegame://' }}" 
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400">
                    <p class="text-xs text-gray-500 mt-1">The custom scheme used to open your app (e.g., bubblegame://). <strong>Note: Changing this requires an app update.</strong></p>
                    
                    <!-- Example Display -->
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-xs text-blue-600 font-semibold mb-1">Example Referral Link:</p>
                        <code class="text-xs text-blue-800 block bg-blue-100/50 p-2 rounded">{{ $settings['deep_link_app_scheme'] ?? 'bubblegame://' }}refer?code=USER_ID</code>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">App Name (Display Name)</label>
                    <input type="text" name="deep_link_app_name" value="{{ $settings['deep_link_app_name'] ?? 'My Game' }}" 
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400">
                    <p class="text-xs text-gray-500 mt-1">Name displayed on the fallback page.</p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-green-600 to-teal-600 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
