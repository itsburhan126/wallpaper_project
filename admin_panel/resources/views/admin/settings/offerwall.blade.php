@extends('layouts.admin')

@section('header', 'Offerwall Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.offerwall.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shadow-sm">
                        <i class="fas fa-coins text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Offerwall Configuration</h3>
                        <p class="text-sm text-slate-500">Manage API keys and URLs for PubScale, Torox, and other offerwalls.</p>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <!-- Offerwall Configuration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">PubScale App ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-rocket text-slate-400"></i>
                            </div>
                            <input type="text" name="pubscale_app_id" value="{{ $settings['pubscale_app_id'] ?? '' }}" 
                                class="w-full pl-10 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all placeholder-slate-400 font-medium text-slate-800">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Torox App ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-fingerprint text-slate-400"></i>
                            </div>
                            <input type="text" name="torox_app_id" value="{{ $settings['torox_app_id'] ?? '' }}" 
                                class="w-full pl-10 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all placeholder-slate-400 font-medium text-slate-800">
                        </div>
                        <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> Enter your Torox App ID (e.g., 12345). The Offerwall URL will be generated automatically.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                <i class="fas fa-save text-lg"></i>
                <span>Save Offerwall Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
