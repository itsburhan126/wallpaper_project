@extends('layouts.admin')

@section('header', 'Lucky Wheel Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.lucky_wheel.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Lucky Wheel Configuration -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600">
                        <i class="fas fa-dharmachakra"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Lucky Wheel Configuration</h3>
                        <p class="text-sm text-slate-500">Configure daily limits and rewards for the Lucky Wheel task</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Daily Limit -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Daily Spin Limit</label>
                        <div class="relative">
                            <input type="number" name="lucky_wheel_limit" value="{{ $settings['lucky_wheel_limit'] ?? 10 }}" class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none" placeholder="e.g. 10">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Maximum number of spins a user can perform per day.</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6">
                     <h4 class="text-sm font-bold text-slate-800 mb-4">Wheel Rewards (8 Segments)</h4>
                     <p class="text-xs text-slate-500 mb-4">Set the coin amount for each slice of the wheel. Order is clockwise.</p>
                     
                     <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @for($i = 1; $i <= 8; $i++)
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Slice {{ $i }}</label>
                            <div class="relative">
                                <input type="number" name="lucky_wheel_reward_{{ $i }}" value="{{ $settings['lucky_wheel_reward_' . $i] ?? ($i * 10) }}" class="w-full pl-8 pr-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none" placeholder="0">
                                <div class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                    <i class="fas fa-coins"></i>
                                </div>
                            </div>
                        </div>
                        @endfor
                     </div>
                </div>
            </div>
        </div>

        <!-- Ad Network Priority (Fallback System) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Ad Network Priority (Fallback)</h3>
                        <p class="text-sm text-slate-500">Define the order in which ad networks should be called after a spin.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- 1st Priority -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">1st Priority (Primary)</label>
                        <select name="lucky_wheel_priority_1" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['lucky_wheel_priority_1'] ?? 'admob') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2nd Priority -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">2nd Priority (Fallback)</label>
                        <select name="lucky_wheel_priority_2" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['lucky_wheel_priority_2'] ?? '') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3rd Priority -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">3rd Priority (Last Resort)</label>
                        <select name="lucky_wheel_priority_3" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white">
                            @foreach(config('ad_networks') as $key => $name)
                                <option value="{{ $key }}" {{ ($settings['lucky_wheel_priority_3'] ?? '') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Save Lucky Wheel Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection