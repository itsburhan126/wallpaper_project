<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class DailyRewardController extends Controller
{
    public function index()
    {
        $keys = [
            'ad_priority_1', 
            'ad_priority_2', 
            'ad_priority_3',
            'daily_reward_1',
            'daily_reward_2',
            'daily_reward_3',
            'daily_reward_4',
            'daily_reward_5',
            'daily_reward_6',
            'daily_reward_7',
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        
        $totalCoins = 0;
        for ($i = 1; $i <= 7; $i++) {
            $totalCoins += (int)($settings['daily_reward_' . $i] ?? 0);
        }
        
        return view('admin.daily-rewards.index', compact('settings', 'totalCoins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'daily_reward_1' => 'required|integer|min:0',
            'daily_reward_2' => 'required|integer|min:0',
            'daily_reward_3' => 'required|integer|min:0',
            'daily_reward_4' => 'required|integer|min:0',
            'daily_reward_5' => 'required|integer|min:0',
            'daily_reward_6' => 'required|integer|min:0',
            'daily_reward_7' => 'required|integer|min:0',
        ]);

        for ($i = 1; $i <= 7; $i++) {
            $key = 'daily_reward_' . $i;
            if ($request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key), 'group' => 'game', 'type' => 'integer']
                );
            }
        }

        return redirect()->back()->with('success', 'Daily rewards updated successfully.');
    }
}
