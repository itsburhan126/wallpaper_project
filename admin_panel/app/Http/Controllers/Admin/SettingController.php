<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function general()
    {
        $settings = Setting::where('group', 'general')->pluck('value', 'key');
        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        return redirect()->back()->with('success', 'General settings updated successfully.');
    }

    public function ads()
    {
        $settings = Setting::where('group', 'ads')->pluck('value', 'key');
        return view('admin.settings.ads', compact('settings'));
    }

    public function updateAds(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'ads');
        }

        return redirect()->back()->with('success', 'Ad settings updated successfully.');
    }

    public function promotion()
    {
        $settings = Setting::where('group', 'promotion')->pluck('value', 'key');
        // Fallback for migrated settings
        if ($settings->isEmpty()) {
            $settings = Setting::where('group', 'ads')
                ->whereIn('key', ['ad_cpc_price', 'ad_cpm_price', 'ad_payment_info'])
                ->pluck('value', 'key');
        }
        return view('admin.settings.promotion', compact('settings'));
    }

    public function updatePromotion(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'promotion');
        }

        return redirect()->back()->with('success', 'Promotion settings updated successfully.');
    }

    public function api()
    {
        // Fetch keys regardless of group to ensure migration from 'general' works smoothly
        $keys = [
            'onesignal_app_id',
            'pusher_app_id', 'pusher_app_key', 'pusher_app_cluster',
            'firebase_api_key', 'firebase_auth_domain', 'firebase_project_id',
            'firebase_storage_bucket', 'firebase_messaging_sender_id',
            'firebase_app_id'
        ];
        
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        return view('admin.settings.api', compact('settings'));
    }

    public function shorts()
    {
        $settings = Setting::where('group', 'shorts')->pluck('value', 'key');
        return view('admin.settings.shorts', compact('settings'));
    }

    public function updateShorts(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'shorts');
        }

        return redirect()->back()->with('success', 'Shorts settings updated successfully.');
    }

    public function updateApi(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'api');
        }

        return redirect()->back()->with('success', 'API settings updated successfully.');
    }

    public function offerwall()
    {
        $keys = [
            'pubscale_app_id', 'torox_app_id'
        ];
        
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        return view('admin.settings.offerwall', compact('settings'));
    }

    public function updateOfferwall(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'offerwall');
        }

        return redirect()->back()->with('success', 'Offerwall settings updated successfully.');
    }

    public function email()
    {
        $keys = [
            'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption',
            'mail_from_address', 'mail_from_name'
        ];
        
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmail(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'email');
        }

        return redirect()->back()->with('success', 'Email settings updated successfully.');
    }

    public function referral()
    {
        $keys = ['signup_bonus', 'referral_bonus_l1', 'referral_bonus_l2', 'referral_bonus_l3'];
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        return view('admin.settings.referral', compact('settings'));
    }

    public function updateReferral(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'referral');
        }

        return redirect()->back()->with('success', 'Referral settings updated successfully.');
    }

    public function game()
    {
        $keys = [
            'game_play_reward_coins', 
            'ad_priority_1',
            'ad_priority_2',
            'ad_priority_3',
            'native_ad_network',
            'admob_native_id',
            'facebook_native_id',
        ];
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        return view('admin.settings.game', compact('settings'));
    }

    public function updateGame(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'game');
        }

        return redirect()->back()->with('success', 'Game settings updated successfully.');
    }

    public function watchAds()
    {
        $keys = [
            'watch_ads_limit', 
            'watch_ads_reward',
            'watch_ads_priority_1',
            'watch_ads_priority_2',
            'watch_ads_priority_3',
        ];
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        return view('admin.settings.watch_ads', compact('settings'));
    }

    public function updateWatchAds(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'watch_ads');
        }

        return redirect()->back()->with('success', 'Watch Ads settings updated successfully.');
    }

    public function luckyWheel()
    {
        $keys = [
            'lucky_wheel_limit', 
            'lucky_wheel_priority_1',
            'lucky_wheel_priority_2',
            'lucky_wheel_priority_3',
        ];
        
        // Add keys for 8 segments
        for ($i = 1; $i <= 8; $i++) {
            $keys[] = 'lucky_wheel_reward_' . $i;
        }

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        return view('admin.settings.lucky_wheel', compact('settings'));
    }

    public function updateLuckyWheel(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'lucky_wheel');
        }

        return redirect()->back()->with('success', 'Lucky Wheel settings updated successfully.');
    }

    public function deepLink()
    {
        $keys = [
            'deep_link_fallback_url', 
            'deep_link_app_scheme', 
            'deep_link_app_name'
        ];
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        return view('admin.settings.deep_link', compact('settings'));
    }

    public function updateDeepLink(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'deep_link');
        }

        return redirect()->back()->with('success', 'Deep link settings updated successfully.');
    }
}
