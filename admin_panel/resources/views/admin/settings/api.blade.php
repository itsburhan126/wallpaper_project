@extends('layouts.admin')

@section('header', 'API Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.api.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- API Configuration (OneSignal, Pusher, Firebase) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-code-branch"></i>
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
                    <h4 class="text-md font-semibold text-slate-700 mb-4 border-b border-slate-100 pb-2">OneSignal Configuration</h4>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">OneSignal App ID</label>
                            <input type="text" name="onesignal_app_id" value="{{ $settings['onesignal_app_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <!-- Pusher -->
                <div>
                    <h4 class="text-md font-semibold text-slate-700 mb-4 border-b border-slate-100 pb-2">Pusher Configuration</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pusher App ID</label>
                            <input type="text" name="pusher_app_id" value="{{ $settings['pusher_app_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pusher App Key</label>
                            <input type="text" name="pusher_app_key" value="{{ $settings['pusher_app_key'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pusher App Cluster</label>
                            <input type="text" name="pusher_app_cluster" value="{{ $settings['pusher_app_cluster'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <!-- Firebase -->
                <div>
                    <h4 class="text-md font-semibold text-slate-700 mb-4 border-b border-slate-100 pb-2">Firebase Configuration</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">API Key</label>
                            <input type="text" name="firebase_api_key" value="{{ $settings['firebase_api_key'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Auth Domain</label>
                            <input type="text" name="firebase_auth_domain" value="{{ $settings['firebase_auth_domain'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Project ID</label>
                            <input type="text" name="firebase_project_id" value="{{ $settings['firebase_project_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Storage Bucket</label>
                            <input type="text" name="firebase_storage_bucket" value="{{ $settings['firebase_storage_bucket'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Messaging Sender ID</label>
                            <input type="text" name="firebase_messaging_sender_id" value="{{ $settings['firebase_messaging_sender_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">App ID</label>
                            <input type="text" name="firebase_app_id" value="{{ $settings['firebase_app_id'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-slate-400">
                        </div>

                    </div>
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
