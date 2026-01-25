@extends('layouts.admin')

@section('header', 'Shorts Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.shorts.update') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Reward Settings -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-gift text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Reward Configuration</h3>
                        <p class="text-sm text-slate-500">Configure rewards for watching short videos</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Reward Timer -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Reward Timer (Seconds)</label>
                        <div class="relative">
                            <input type="number" name="shorts_reward_timer" required min="5" 
                                value="{{ $settings['shorts_reward_timer'] ?? 15 }}"
                                class="w-full pl-10 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-800">
                            <div class="absolute left-4 top-3.5 text-slate-400">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> How many seconds a user must watch before the reward button appears.
                        </p>
                    </div>

                    <!-- Reward Amount (Coins) -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Reward Amount (Coins)</label>
                        <div class="relative">
                            <input type="number" name="shorts_reward_coins" required min="1" 
                                value="{{ $settings['shorts_reward_coins'] ?? 10 }}"
                                class="w-full pl-10 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-800">
                            <div class="absolute left-4 top-3.5 text-slate-400">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> Coins awarded after watching the ad.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ad Fallback Settings -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Ad Fallback Configuration</h3>
                        <p class="text-sm text-slate-500">Configure ad networks priority for shorts</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Fallback 1 -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">1st Priority</label>
                        <select name="ad_fallback_1" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all bg-white font-medium text-slate-800">
                            <option value="admob" {{ ($settings['ad_fallback_1'] ?? 'admob') == 'admob' ? 'selected' : '' }}>AdMob</option>
                            <option value="applovin" {{ ($settings['ad_fallback_1'] ?? '') == 'applovin' ? 'selected' : '' }}>AppLovin</option>
                            <option value="unity" {{ ($settings['ad_fallback_1'] ?? '') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                            <option value="facebook" {{ ($settings['ad_fallback_1'] ?? '') == 'facebook' ? 'selected' : '' }}>Meta (Facebook)</option>
                            <option value="startapp" {{ ($settings['ad_fallback_1'] ?? '') == 'startapp' ? 'selected' : '' }}>StartApp</option>
                            <option value="none" {{ ($settings['ad_fallback_1'] ?? '') == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <!-- Fallback 2 -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">2nd Priority</label>
                        <select name="ad_fallback_2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all bg-white font-medium text-slate-800">
                            <option value="admob" {{ ($settings['ad_fallback_2'] ?? '') == 'admob' ? 'selected' : '' }}>AdMob</option>
                            <option value="applovin" {{ ($settings['ad_fallback_2'] ?? '') == 'applovin' ? 'selected' : '' }}>AppLovin</option>
                            <option value="unity" {{ ($settings['ad_fallback_2'] ?? '') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                            <option value="facebook" {{ ($settings['ad_fallback_2'] ?? '') == 'facebook' ? 'selected' : '' }}>Meta (Facebook)</option>
                            <option value="startapp" {{ ($settings['ad_fallback_2'] ?? '') == 'startapp' ? 'selected' : '' }}>StartApp</option>
                            <option value="none" {{ ($settings['ad_fallback_2'] ?? 'none') == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <!-- Fallback 3 -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">3rd Priority</label>
                        <select name="ad_fallback_3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all bg-white font-medium text-slate-800">
                            <option value="admob" {{ ($settings['ad_fallback_3'] ?? '') == 'admob' ? 'selected' : '' }}>AdMob</option>
                            <option value="applovin" {{ ($settings['ad_fallback_3'] ?? '') == 'applovin' ? 'selected' : '' }}>AppLovin</option>
                            <option value="unity" {{ ($settings['ad_fallback_3'] ?? '') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                            <option value="facebook" {{ ($settings['ad_fallback_3'] ?? '') == 'facebook' ? 'selected' : '' }}>Meta (Facebook)</option>
                            <option value="startapp" {{ ($settings['ad_fallback_3'] ?? '') == 'startapp' ? 'selected' : '' }}>StartApp</option>
                            <option value="none" {{ ($settings['ad_fallback_3'] ?? 'none') == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                <i class="fas fa-save text-lg"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection