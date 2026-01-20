@extends('layouts.admin')

@section('title', 'Daily Rewards')

@section('content')
<div class="container-fluid">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daily Rewards</h1>
            <p class="text-slate-500 text-sm mt-1">Configure daily check-in rewards and ad fallbacks.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center text-emerald-700">
            <i class="fas fa-check-circle mr-3 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Ad Network Configuration -->
    <div class="mb-8">
        <form action="{{ route('admin.settings.ads.update') }}" method="POST">
            @csrf
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Ad Fallback Configuration</h3>
                            <p class="text-sm text-slate-500">Configure ad network priorities for daily rewards.</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                    <!-- Fallback Priority System -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- 1st Priority -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">1st Priority</label>
                            <select name="ad_priority_1" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white text-slate-700">
                                @foreach(config('ad_networks') as $key => $name)
                                    <option value="{{ $key }}" {{ ($settings['ad_priority_1'] ?? 'admob') == $key ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2nd Priority -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">2nd Priority</label>
                            <select name="ad_priority_2" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white text-slate-700">
                                @foreach(config('ad_networks') as $key => $name)
                                    <option value="{{ $key }}" {{ ($settings['ad_priority_2'] ?? '') == $key ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3rd Priority -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">3rd Priority</label>
                            <select name="ad_priority_3" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white text-slate-700">
                                @foreach(config('ad_networks') as $key => $name)
                                    <option value="{{ $key }}" {{ ($settings['ad_priority_3'] ?? '') == $key ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 mt-6 border-t border-slate-100">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all">
                            <i class="fas fa-save mr-2"></i> Save Ad Configuration
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Daily Rewards Configuration -->
    <div class="mb-8">
        <form action="{{ route('admin.daily_rewards.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">7-Day Reward Cycle</h3>
                            <p class="text-sm text-slate-500">Configure coin rewards for each day of the streak.</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @for ($i = 1; $i <= 7; $i++)
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Day {{ $i }} (Coins)</label>
                            <input type="number" name="daily_reward_{{ $i }}" value="{{ $settings['daily_reward_'.$i] ?? ($i * 10) }}" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-emerald-200 transition-all bg-slate-50" min="0">
                        </div>
                        @endfor
                    </div>

                    <div class="flex justify-end pt-6 mt-6 border-t border-slate-100">
                        <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm shadow-emerald-200 transition-all">
                            <i class="fas fa-save mr-2"></i> Save Rewards
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
