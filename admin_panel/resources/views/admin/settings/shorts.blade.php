@extends('layouts.admin')

@section('header', 'Shorts Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Shorts Settings</h1>
        <p class="text-sm text-slate-500 mt-1">Configure global settings for short videos</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2 mb-6">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        <!-- Reward Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="fas fa-gift text-indigo-500"></i>
                <h3 class="font-semibold text-slate-800">Reward Configuration</h3>
            </div>
            
            <form action="{{ route('admin.settings.shorts.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Reward Timer -->
                    <div>
                        <label for="shorts_reward_timer" class="block text-sm font-semibold text-slate-700 mb-2">Reward Timer (Seconds) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-stopwatch absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="number" name="shorts_reward_timer" id="shorts_reward_timer" required min="5" 
                                value="{{ $settings['shorts_reward_timer'] ?? 15 }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">How many seconds a user must watch before the reward button appears.</p>
                    </div>

                    <!-- Reward Amount (Coins) -->
                    <div>
                        <label for="shorts_reward_coins" class="block text-sm font-semibold text-slate-700 mb-2">Reward Amount (Coins)</label>
                        <div class="relative">
                            <i class="fas fa-coins absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="number" name="shorts_reward_coins" id="shorts_reward_coins" required min="1" 
                                value="{{ $settings['shorts_reward_coins'] ?? 10 }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">Coins awarded after watching the ad.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all text-sm font-medium shadow-md shadow-indigo-500/20 flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Ad Fallback Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="fas fa-ad text-indigo-500"></i>
                <h3 class="font-semibold text-slate-800">Ad Fallback Configuration</h3>
            </div>
            
            <form action="{{ route('admin.settings.shorts.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Fallback 1 -->
                    <div>
                        <label for="ad_fallback_1" class="block text-sm font-semibold text-slate-700 mb-2">Fallback Priority 1</label>
                        <select name="ad_fallback_1" id="ad_fallback_1" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm">
                            <option value="admob" {{ ($settings['ad_fallback_1'] ?? 'admob') == 'admob' ? 'selected' : '' }}>AdMob</option>
                            <option value="applovin" {{ ($settings['ad_fallback_1'] ?? '') == 'applovin' ? 'selected' : '' }}>AppLovin</option>
                            <option value="unity" {{ ($settings['ad_fallback_1'] ?? '') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                            <option value="facebook" {{ ($settings['ad_fallback_1'] ?? '') == 'facebook' ? 'selected' : '' }}>Meta (Facebook)</option>
                            <option value="startapp" {{ ($settings['ad_fallback_1'] ?? '') == 'startapp' ? 'selected' : '' }}>StartApp</option>
                            <option value="none" {{ ($settings['ad_fallback_1'] ?? '') == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1.5">First ad network to try.</p>
                    </div>

                    <!-- Fallback 2 -->
                    <div>
                        <label for="ad_fallback_2" class="block text-sm font-semibold text-slate-700 mb-2">Fallback Priority 2</label>
                        <select name="ad_fallback_2" id="ad_fallback_2" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm">
                            <option value="admob" {{ ($settings['ad_fallback_2'] ?? '') == 'admob' ? 'selected' : '' }}>AdMob</option>
                            <option value="applovin" {{ ($settings['ad_fallback_2'] ?? '') == 'applovin' ? 'selected' : '' }}>AppLovin</option>
                            <option value="unity" {{ ($settings['ad_fallback_2'] ?? '') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                            <option value="facebook" {{ ($settings['ad_fallback_2'] ?? '') == 'facebook' ? 'selected' : '' }}>Meta (Facebook)</option>
                            <option value="startapp" {{ ($settings['ad_fallback_2'] ?? '') == 'startapp' ? 'selected' : '' }}>StartApp</option>
                            <option value="none" {{ ($settings['ad_fallback_2'] ?? 'none') == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1.5">If Priority 1 fails, try this.</p>
                    </div>

                    <!-- Fallback 3 -->
                    <div>
                        <label for="ad_fallback_3" class="block text-sm font-semibold text-slate-700 mb-2">Fallback Priority 3</label>
                        <select name="ad_fallback_3" id="ad_fallback_3" class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm">
                            <option value="admob" {{ ($settings['ad_fallback_3'] ?? '') == 'admob' ? 'selected' : '' }}>AdMob</option>
                            <option value="applovin" {{ ($settings['ad_fallback_3'] ?? '') == 'applovin' ? 'selected' : '' }}>AppLovin</option>
                            <option value="unity" {{ ($settings['ad_fallback_3'] ?? '') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                            <option value="facebook" {{ ($settings['ad_fallback_3'] ?? '') == 'facebook' ? 'selected' : '' }}>Meta (Facebook)</option>
                            <option value="startapp" {{ ($settings['ad_fallback_3'] ?? '') == 'startapp' ? 'selected' : '' }}>StartApp</option>
                            <option value="none" {{ ($settings['ad_fallback_3'] ?? 'none') == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1.5">Final backup ad network.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all text-sm font-medium shadow-md shadow-indigo-500/20 flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection