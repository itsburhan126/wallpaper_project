@extends('layouts.admin')

@section('title', 'Offerwall Settings')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Offerwall Settings</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-8">
            <form action="{{ route('admin.settings.offerwall.update') }}" method="POST">
                @csrf
                
                <div class="flex items-center mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg mr-4">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Offerwall Configuration</h3>
                        <p class="text-sm text-gray-500">Manage API keys and URLs for PubScale, Torox, and other offerwalls.</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <!-- Offerwall Configuration -->
                    <div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">PubScale App ID</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-rocket text-gray-400"></i>
                                    </div>
                                    <input type="text" name="pubscale_app_id" value="{{ $settings['pubscale_app_id'] ?? '' }}" 
                                        class="w-full pl-10 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Torox App ID</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-fingerprint text-gray-400"></i>
                                    </div>
                                    <input type="text" name="torox_app_id" value="{{ $settings['torox_app_id'] ?? '' }}" 
                                        class="w-full pl-10 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Enter your Torox App ID (e.g., 12345). The Offerwall URL will be generated automatically.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
