@extends('layouts.admin')

@section('header', 'Game & Ad Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.game.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Game Play Settings -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-gamepad text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Game Play Settings</h3>
                        <p class="text-sm text-slate-500">Configure rewards for gameplay</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <!-- Daily Game Limit -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Daily Game Limit</label>
                    <div class="relative">
                        <input type="number" name="game_daily_limit" value="{{ $settings['game_daily_limit'] ?? 10 }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all pl-12 font-medium text-slate-800">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-hand-paper"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i> Maximum number of games a user can play per day.
                    </p>
                </div>
            </div>
        </div>

        <!-- Ad Network Priority (Fallback System) -->
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Ad Network Priority (Fallback)</h3>
                        <p class="text-sm text-slate-500">Define the order in which ad networks should be called (Waterfall).</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- 1st Priority -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">1st Priority (Primary)</label>
                        <select name="ad_priority_1" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all bg-white font-medium text-slate-800">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['ad_priority_1'] ?? 'admob') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2nd Priority -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">2nd Priority (Fallback)</label>
                        <select name="ad_priority_2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all bg-white font-medium text-slate-800">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['ad_priority_2'] ?? '') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3rd Priority -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">3rd Priority (Last Resort)</label>
                        <select name="ad_priority_3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all bg-white font-medium text-slate-800">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['ad_priority_3'] ?? '') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                <i class="fas fa-save text-lg"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
