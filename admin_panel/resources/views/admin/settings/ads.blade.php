@extends('layouts.admin')

@section('header', 'Ad Configuration')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.ads.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- General Ad Configuration -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">General Configuration</h3>
                        <p class="text-sm text-slate-500">Select your preferred ad network</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="max-w-md">
                    <label class="block text-sm font-medium text-slate-700 mb-4">Active Ad Strategy</label>
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Only AdMob -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="ad_provider" value="admob_only" class="peer sr-only" {{ ($settings['ad_provider'] ?? '') == 'admob_only' ? 'checked' : '' }}>
                            <div class="p-4 bg-white border border-slate-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                        <i class="fab fa-google"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-900">Only AdMob</h4>
                                        <p class="text-xs text-slate-500">Display only Google AdMob ads throughout the app.</p>
                                    </div>
                                    <div class="ml-auto text-blue-600 hidden peer-checked:block">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Only Facebook -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="ad_provider" value="facebook_only" class="peer sr-only" {{ ($settings['ad_provider'] ?? '') == 'facebook_only' ? 'checked' : '' }}>
                            <div class="p-4 bg-white border border-slate-200 rounded-lg peer-checked:border-blue-700 peer-checked:bg-blue-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center text-white">
                                        <i class="fab fa-facebook-f"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-900">Only Facebook</h4>
                                        <p class="text-xs text-slate-500">Display only Facebook Audience Network ads.</p>
                                    </div>
                                    <div class="ml-auto text-blue-700 hidden peer-checked:block">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="mt-8 border-t border-slate-200 pt-8">
                        <label class="block text-sm font-medium text-slate-700 mb-4">Ad Visibility Control</label>
                        
                        <!-- Global Master Switch -->
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 mb-4">
                            <div>
                                <h4 class="font-semibold text-slate-900">Enable Ads Globally</h4>
                                <p class="text-sm text-slate-500">Master switch to show/hide all ads in the app</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="ads_enabled" value="0">
                                <input type="checkbox" name="ads_enabled" value="1" class="sr-only peer" {{ ($settings['ads_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Banner Ads Switch -->
                            <div class="p-4 bg-white border border-slate-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                        <i class="far fa-window-maximize"></i>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="banner_ads_enabled" value="0">
                                        <input type="checkbox" name="banner_ads_enabled" value="1" class="sr-only peer" {{ ($settings['banner_ads_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <h5 class="font-medium text-slate-900">Banner Ads</h5>
                                <p class="text-xs text-slate-500">Top/Bottom banners</p>
                            </div>

                            <!-- Interstitial Ads Switch -->
                            <div class="p-4 bg-white border border-slate-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                                        <i class="far fa-clone"></i>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="interstitial_ads_enabled" value="0">
                                        <input type="checkbox" name="interstitial_ads_enabled" value="1" class="sr-only peer" {{ ($settings['interstitial_ads_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-600"></div>
                                    </label>
                                </div>
                                <h5 class="font-medium text-slate-900">Interstitial Ads</h5>
                                <p class="text-xs text-slate-500">Full screen popups</p>
                            </div>

                            <!-- Native Ads Switch -->
                            <div class="p-4 bg-white border border-slate-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="p-2 bg-green-100 rounded-lg text-green-600">
                                        <i class="far fa-newspaper"></i>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="native_ads_enabled" value="0">
                                        <input type="checkbox" name="native_ads_enabled" value="1" class="sr-only peer" {{ ($settings['native_ads_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                                    </label>
                                </div>
                                <h5 class="font-medium text-slate-900">Native Ads</h5>
                                <p class="text-xs text-slate-500">In-feed ads</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AdMob Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-ad"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">AdMob Configuration</h3>
                        <p class="text-sm text-slate-500">Manage your Google AdMob ad units</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Android -->
                <div class="space-y-6">
                    <h4 class="font-semibold text-slate-700 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fab fa-android text-green-500"></i> Android
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">App ID</label>
                        <input type="text" name="admob_android_app_id" value="{{ $settings['admob_android_app_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx~yyyyyyyyyy">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Banner Unit ID</label>
                        <input type="text" name="admob_android_banner_id" value="{{ $settings['admob_android_banner_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/yyyyyyyyyy">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Interstitial Unit ID</label>
                        <input type="text" name="admob_android_interstitial_id" value="{{ $settings['admob_android_interstitial_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/yyyyyyyyyy">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rewarded Unit ID</label>
                        <input type="text" name="admob_android_rewarded_id" value="{{ $settings['admob_android_rewarded_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/yyyyyyyyyy">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Native Unit ID</label>
                        <input type="text" name="admob_android_native_id" value="{{ $settings['admob_android_native_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/yyyyyyyyyy">
                    </div>
                </div>

                <!-- iOS -->
                <div class="space-y-6">
                    <h4 class="font-semibold text-slate-700 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fab fa-apple text-slate-800"></i> iOS
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">App ID</label>
                        <input type="text" name="admob_ios_app_id" value="{{ $settings['admob_ios_app_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx~yyyyyyyyyy">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Banner Unit ID</label>
                        <input type="text" name="admob_ios_banner_id" value="{{ $settings['admob_ios_banner_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/yyyyyyyyyy">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Interstitial Unit ID</label>
                        <input type="text" name="admob_ios_interstitial_id" value="{{ $settings['admob_ios_interstitial_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/yyyyyyyyyy">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rewarded Unit ID</label>
                        <input type="text" name="admob_ios_rewarded_id" value="{{ $settings['admob_ios_rewarded_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/yyyyyyyyyy">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Native Unit ID</label>
                        <input type="text" name="admob_ios_native_id" value="{{ $settings['admob_ios_native_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/yyyyyyyyyy">
                    </div>
                </div>
            </div>
        </div>

        <!-- Facebook Audience Network Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-700 flex items-center justify-center text-white">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Facebook Audience Network</h3>
                        <p class="text-sm text-slate-500">Manage your Facebook ad placements</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Android -->
                <div class="space-y-6">
                    <h4 class="font-semibold text-slate-700 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fab fa-android text-green-500"></i> Android
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Banner Placement ID</label>
                        <input type="text" name="facebook_android_banner_id" value="{{ $settings['facebook_android_banner_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-700 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Interstitial Placement ID</label>
                        <input type="text" name="facebook_android_interstitial_id" value="{{ $settings['facebook_android_interstitial_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-700 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Native Placement ID</label>
                        <input type="text" name="facebook_android_native_id" value="{{ $settings['facebook_android_native_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-700 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rewarded Placement ID</label>
                        <input type="text" name="facebook_android_rewarded_id" value="{{ $settings['facebook_android_rewarded_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-700 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>
                </div>

                <!-- iOS -->
                <div class="space-y-6">
                    <h4 class="font-semibold text-slate-700 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fab fa-apple text-slate-800"></i> iOS
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Banner Placement ID</label>
                        <input type="text" name="facebook_ios_banner_id" value="{{ $settings['facebook_ios_banner_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-700 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Interstitial Placement ID</label>
                        <input type="text" name="facebook_ios_interstitial_id" value="{{ $settings['facebook_ios_interstitial_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-700 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Native Placement ID</label>
                        <input type="text" name="facebook_ios_native_id" value="{{ $settings['facebook_ios_native_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-700 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rewarded Placement ID</label>
                        <input type="text" name="facebook_ios_rewarded_id" value="{{ $settings['facebook_ios_rewarded_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-blue-700 focus:ring-2 focus:ring-blue-200 transition-all placeholder-slate-400"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>
                </div>
            </div>
        </div>

        <!-- Unity Ads Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-white">
                        <i class="fab fa-unity"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Unity Ads Configuration</h3>
                        <p class="text-sm text-slate-500">Manage your Unity ad placements</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Android -->
                <div class="space-y-6">
                    <h4 class="font-semibold text-slate-700 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fab fa-android text-green-500"></i> Android
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Game ID</label>
                        <input type="text" name="unity_android_game_id" value="{{ $settings['unity_android_game_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 transition-all placeholder-slate-400"
                            placeholder="1234567">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Banner Placement ID</label>
                        <input type="text" name="unity_android_banner_id" value="{{ $settings['unity_android_banner_id'] ?? 'banner' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 transition-all placeholder-slate-400"
                            placeholder="banner">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Interstitial Placement ID</label>
                        <input type="text" name="unity_android_interstitial_id" value="{{ $settings['unity_android_interstitial_id'] ?? 'video' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 transition-all placeholder-slate-400"
                            placeholder="video">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rewarded Placement ID</label>
                        <input type="text" name="unity_android_rewarded_id" value="{{ $settings['unity_android_rewarded_id'] ?? 'rewardedVideo' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 transition-all placeholder-slate-400"
                            placeholder="rewardedVideo">
                    </div>
                </div>

                <!-- iOS -->
                <div class="space-y-6">
                    <h4 class="font-semibold text-slate-700 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fab fa-apple text-slate-800"></i> iOS
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Game ID</label>
                        <input type="text" name="unity_ios_game_id" value="{{ $settings['unity_ios_game_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 transition-all placeholder-slate-400"
                            placeholder="1234567">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Banner Placement ID</label>
                        <input type="text" name="unity_ios_banner_id" value="{{ $settings['unity_ios_banner_id'] ?? 'banner' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 transition-all placeholder-slate-400"
                            placeholder="banner">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Interstitial Placement ID</label>
                        <input type="text" name="unity_ios_interstitial_id" value="{{ $settings['unity_ios_interstitial_id'] ?? 'video' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 transition-all placeholder-slate-400"
                            placeholder="video">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rewarded Placement ID</label>
                        <input type="text" name="unity_ios_rewarded_id" value="{{ $settings['unity_ios_rewarded_id'] ?? 'rewardedVideo' }}" 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-200 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 transition-all placeholder-slate-400"
                            placeholder="rewardedVideo">
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Save Configuration</span>
            </button>
        </div>
    </form>
</div>
@endsection
