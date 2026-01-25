@extends('layouts.admin')

@section('header', 'API Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.api.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- API Configuration (OneSignal, Pusher, Firebase) -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-code-branch text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">API Configuration</h3>
                        <p class="text-sm text-slate-500">OneSignal, Pusher, and Firebase Settings</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-8">
                <!-- OneSignal -->
                <div>
                    <h4 class="text-md font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500">
                            <i class="fas fa-bell"></i>
                        </div>
                        OneSignal Configuration
                    </h4>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">OneSignal App ID</label>
                            <input type="text" name="onesignal_app_id" value="{{ $settings['onesignal_app_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                    </div>
                </div>

                <!-- Pusher -->
                <div>
                    <h4 class="text-md font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500">
                            <i class="fas fa-satellite-dish"></i>
                        </div>
                        Pusher Configuration
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pusher App ID</label>
                            <input type="text" name="pusher_app_id" value="{{ $settings['pusher_app_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pusher App Key</label>
                            <input type="text" name="pusher_app_key" value="{{ $settings['pusher_app_key'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pusher App Cluster</label>
                            <input type="text" name="pusher_app_cluster" value="{{ $settings['pusher_app_cluster'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                    </div>
                </div>

                <!-- Firebase -->
                <div>
                    <h4 class="text-md font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500">
                            <i class="fas fa-fire"></i>
                        </div>
                        Firebase Configuration
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">API Key</label>
                            <input type="text" name="firebase_api_key" value="{{ $settings['firebase_api_key'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Auth Domain</label>
                            <input type="text" name="firebase_auth_domain" value="{{ $settings['firebase_auth_domain'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Project ID</label>
                            <input type="text" name="firebase_project_id" value="{{ $settings['firebase_project_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Storage Bucket</label>
                            <input type="text" name="firebase_storage_bucket" value="{{ $settings['firebase_storage_bucket'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Messaging Sender ID</label>
                            <input type="text" name="firebase_messaging_sender_id" value="{{ $settings['firebase_messaging_sender_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">App ID</label>
                            <input type="text" name="firebase_app_id" value="{{ $settings['firebase_app_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all placeholder-slate-400 font-medium">
                        </div>

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
