@extends('layouts.admin')

@section('header', 'Watch Ads Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.watch_ads.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Watch Ads Configuration -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Watch Ads Configuration</h3>
                        <p class="text-sm text-slate-500">Configure daily limits and rewards for watching ads</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Daily Limit -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Daily Limit</label>
                        <div class="relative">
                            <input type="number" name="watch_ads_limit" value="{{ $settings['watch_ads_limit'] ?? 10 }}" class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none" placeholder="e.g. 10">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Maximum number of ads a user can watch per day.</p>
                    </div>

                    <!-- Coin Reward -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Coin Reward Per Ad</label>
                        <div class="relative">
                            <input type="number" name="watch_ads_reward" value="{{ $settings['watch_ads_reward'] ?? 50 }}" class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none" placeholder="e.g. 50">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Coins rewarded for each completed ad view.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ad Network Priority (Fallback System) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Ad Network Priority (Fallback)</h3>
                        <p class="text-sm text-slate-500">Define the order in which ad networks should be called for Watch Ads task.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- 1st Priority -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">1st Priority (Primary)</label>
                        <select name="watch_ads_priority_1" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['watch_ads_priority_1'] ?? 'admob') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2nd Priority -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">2nd Priority (Fallback)</label>
                        <select name="watch_ads_priority_2" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['watch_ads_priority_2'] ?? '') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3rd Priority -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">3rd Priority (Last Resort)</label>
                        <select name="watch_ads_priority_3" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['watch_ads_priority_3'] ?? '') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
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