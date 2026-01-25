@extends('layouts.admin')

@section('header', 'Security Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Security Settings</h1>
            <p class="text-slate-500 text-sm mt-1">Manage application security and restrictions</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.security.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600 shadow-sm">
                        <i class="fas fa-shield-alt text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Security Controls</h3>
                        <p class="text-sm text-slate-500">Enable or disable security checks</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-8">
                <!-- VPN Block -->
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-700">VPN Block</h4>
                        <p class="text-sm text-slate-500 mt-1">Prevent users from accessing the app using VPN</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="security_vpn_block" value="0">
                        <input type="checkbox" name="security_vpn_block" value="1" class="sr-only peer" {{ ($settings['security_vpn_block'] ?? 0) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>

                <!-- One Device One Account -->
                <div class="flex items-center justify-between border-t border-slate-100 pt-6">
                    <div>
                        <h4 class="font-bold text-slate-700">One Device One Account</h4>
                        <p class="text-sm text-slate-500 mt-1">Restrict users to a single device per account</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="security_one_device" value="0">
                        <input type="checkbox" name="security_one_device" value="1" class="sr-only peer" {{ ($settings['security_one_device'] ?? 0) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>

                <!-- Root Device Block -->
                <div class="flex items-center justify-between border-t border-slate-100 pt-6">
                    <div>
                        <h4 class="font-bold text-slate-700">Root / Jailbreak Block</h4>
                        <p class="text-sm text-slate-500 mt-1">Block access from rooted or jailbroken devices</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="security_root_block" value="0">
                        <input type="checkbox" name="security_root_block" value="1" class="sr-only peer" {{ ($settings['security_root_block'] ?? 0) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>

                <!-- Developer Mode Block -->
                <div class="flex items-center justify-between border-t border-slate-100 pt-6">
                    <div>
                        <h4 class="font-bold text-slate-700">Developer Mode Block</h4>
                        <p class="text-sm text-slate-500 mt-1">Prevent access if Developer Options are enabled</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="security_dev_mode_block" value="0">
                        <input type="checkbox" name="security_dev_mode_block" value="1" class="sr-only peer" {{ ($settings['security_dev_mode_block'] ?? 0) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>

                <!-- Emulator Block (Extra Security) -->
                <div class="flex items-center justify-between border-t border-slate-100 pt-6">
                    <div>
                        <h4 class="font-bold text-slate-700">Emulator Block</h4>
                        <p class="text-sm text-slate-500 mt-1">Block access from Emulators</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="security_emulator_block" value="0">
                        <input type="checkbox" name="security_emulator_block" value="1" class="sr-only peer" {{ ($settings['security_emulator_block'] ?? 0) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>

            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold rounded-xl shadow-lg shadow-red-500/30 hover:shadow-red-500/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                <i class="fas fa-save text-lg"></i>
                <span>Save Security Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
