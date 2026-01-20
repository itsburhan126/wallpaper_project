<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyReward;

use App\Models\Setting;

class DailyRewardController extends Controller
{
    public function index()
    {
        $rewards = DailyReward::orderBy('day')->get();
        $settings = Setting::where('group', 'ads')->pluck('value', 'key');
        return view('admin.daily-rewards.index', compact('rewards', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'coins' => 'required|integer|min:0',
            'gems' => 'required|integer|min:0',
        ]);

        $reward = DailyReward::findOrFail($id);
        $reward->update([
            'coins' => $request->coins,
            'gems' => $request->gems,
        ]);

        return redirect()->back()->with('success', 'Daily Reward updated successfully.');
    }
}
