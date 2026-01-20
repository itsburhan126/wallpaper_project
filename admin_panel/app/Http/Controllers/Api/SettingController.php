<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Return all ads settings as a key-value pair object
        $settings = Setting::where('group', 'ads')->pluck('value', 'key');
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function general()
    {
        $general = Setting::where('group', 'general')->pluck('value', 'key');
        $watchAds = Setting::where('group', 'watch_ads')->pluck('value', 'key');
        $luckyWheel = Setting::where('group', 'lucky_wheel')->pluck('value', 'key');
        
        $settings = $general->merge($watchAds)->merge($luckyWheel);
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function api()
    {
        $settings = Setting::where('group', 'api')->pluck('value', 'key');
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function game()
    {
        $settings = Setting::where('group', 'game')->pluck('value', 'key');
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function offerwall()
    {
        $settings = Setting::where('group', 'offerwall')->pluck('value', 'key');
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function shorts()
    {
        $settings = Setting::where('group', 'shorts')->pluck('value', 'key');
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}
