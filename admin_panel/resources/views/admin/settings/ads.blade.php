@extends('layouts.admin')

@section('header', 'Ad Configuration')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.ads.update') }}" method="POST" class="space-y-6">
        @csrf
        


        <!-- AdMob Settings -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mb-6" x-show="activeTab === 'admob'">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fab fa-google text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">AdMob Configuration</h3>
                        <p class="text-sm text-slate-500">Manage your Google AdMob ad units</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="max-w-3xl grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-slate-700 mb-2">AdMob App ID (Android)</label>
                        <input type="text" name="admob_android_app_id" value="{{ $settings['admob_android_app_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-400 font-medium" placeholder="ca-app-pub-xxxxxxxxxxxxxxxx~xxxxxxxxxx">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Banner Ad Unit ID</label>
                        <input type="text" name="admob_android_banner_id" value="{{ $settings['admob_android_banner_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-400 font-medium" placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Interstitial Ad Unit ID</label>
                        <input type="text" name="admob_android_interstitial_id" value="{{ $settings['admob_android_interstitial_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-400 font-medium" placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Rewarded Ad Unit ID</label>
                        <input type="text" name="admob_android_rewarded_id" value="{{ $settings['admob_android_rewarded_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-400 font-medium" placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Native Ad Unit ID</label>
                        <input type="text" name="admob_android_native_id" value="{{ $settings['admob_android_native_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-400 font-medium" placeholder="ca-app-pub-xxxxxxxxxxxxxxxx/xxxxxxxxxx">
                    </div>
                </div>
            </div>
        </div>

        <!-- PubScale Settings -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">PubScale Configuration</h3>
                        <p class="text-sm text-slate-500">Manage your PubScale Offerwall credentials</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="max-w-3xl grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-slate-700 mb-2">PubScale App ID</label>
                        <input type="text" name="pubscale_app_id" value="{{ $settings['pubscale_app_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium" placeholder="Enter your PubScale App ID">
                    </div>
                    
                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-slate-700 mb-2">PubScale API Key</label>
                        <input type="text" name="pubscale_api_key" value="{{ $settings['pubscale_api_key'] ?? '' }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder-slate-400 font-medium" placeholder="Enter your PubScale API Key">
                    </div>
                </div>
            </div>
        </div>

        <!-- Facebook Audience Network Settings -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-sm shadow-blue-500/30">
                        <i class="fab fa-facebook-f text-lg"></i>
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
                    <h4 class="font-bold text-slate-700 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-500">
                            <i class="fab fa-android"></i>
                        </div>
                        Android
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Banner Placement ID</label>
                        <input type="text" name="facebook_android_banner_id" value="{{ $settings['facebook_android_banner_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Interstitial Placement ID</label>
                        <input type="text" name="facebook_android_interstitial_id" value="{{ $settings['facebook_android_interstitial_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Native Placement ID</label>
                        <input type="text" name="facebook_android_native_id" value="{{ $settings['facebook_android_native_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Rewarded Placement ID</label>
                        <input type="text" name="facebook_android_rewarded_id" value="{{ $settings['facebook_android_rewarded_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>
                </div>

                <!-- iOS -->
                <div class="space-y-6">
                    <h4 class="font-bold text-slate-700 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-800">
                            <i class="fab fa-apple"></i>
                        </div>
                        iOS
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Banner Placement ID</label>
                        <input type="text" name="facebook_ios_banner_id" value="{{ $settings['facebook_ios_banner_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Interstitial Placement ID</label>
                        <input type="text" name="facebook_ios_interstitial_id" value="{{ $settings['facebook_ios_interstitial_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Native Placement ID</label>
                        <input type="text" name="facebook_ios_native_id" value="{{ $settings['facebook_ios_native_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Rewarded Placement ID</label>
                        <input type="text" name="facebook_ios_rewarded_id" value="{{ $settings['facebook_ios_rewarded_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="YOUR_PLACEMENT_ID">
                    </div>
                </div>
            </div>
        </div>

        <!-- Unity Ads Settings -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mt-6">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-white shadow-sm shadow-slate-800/30">
                        <i class="fab fa-unity text-lg"></i>
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
                    <h4 class="font-bold text-slate-700 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-500">
                            <i class="fab fa-android"></i>
                        </div>
                        Android
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Game ID</label>
                        <input type="text" name="unity_android_game_id" value="{{ $settings['unity_android_game_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="1234567">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Banner Placement ID</label>
                        <input type="text" name="unity_android_banner_id" value="{{ $settings['unity_android_banner_id'] ?? 'banner' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="banner">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Interstitial Placement ID</label>
                        <input type="text" name="unity_android_interstitial_id" value="{{ $settings['unity_android_interstitial_id'] ?? 'video' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="video">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Rewarded Placement ID</label>
                        <input type="text" name="unity_android_rewarded_id" value="{{ $settings['unity_android_rewarded_id'] ?? 'rewardedVideo' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="rewardedVideo">
                    </div>
                </div>

                <!-- iOS -->
                <div class="space-y-6">
                    <h4 class="font-bold text-slate-700 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-800">
                            <i class="fab fa-apple"></i>
                        </div>
                        iOS
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Game ID</label>
                        <input type="text" name="unity_ios_game_id" value="{{ $settings['unity_ios_game_id'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="1234567">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Banner Placement ID</label>
                        <input type="text" name="unity_ios_banner_id" value="{{ $settings['unity_ios_banner_id'] ?? 'banner' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="banner">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Interstitial Placement ID</label>
                        <input type="text" name="unity_ios_interstitial_id" value="{{ $settings['unity_ios_interstitial_id'] ?? 'video' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="video">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Rewarded Placement ID</label>
                        <input type="text" name="unity_ios_rewarded_id" value="{{ $settings['unity_ios_rewarded_id'] ?? 'rewardedVideo' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 transition-all placeholder-slate-400 font-medium"
                            placeholder="rewardedVideo">
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
