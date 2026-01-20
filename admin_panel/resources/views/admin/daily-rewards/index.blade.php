@extends('layouts.admin')

@section('title', 'Daily Rewards')

@section('content')
<div class="container-fluid">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daily Rewards</h1>
            <p class="text-slate-500 text-sm mt-1">Configure the 15-day cycle rewards for users.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center text-emerald-700">
            <i class="fas fa-check-circle mr-3 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Ad Network Configuration (Moved from Ads Settings) -->
    <div class="mb-8">
        <form action="{{ route('admin.settings.ads.update') }}" method="POST">
            @csrf
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">General Configuration</h3>
                            <p class="text-sm text-slate-500">Select your preferred ad network</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                    <!-- Fallback Priority System -->
                    <div class="mb-10">
                        <h4 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-layer-group text-indigo-500"></i> Ad Network Priority
                        </h4>
                        <p class="text-sm text-slate-500 mb-6">Define the order in which ad networks should be called. If the first fails, the system will try the next.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- 1st Priority -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">1st Priority (Primary)</label>
                                <select name="ad_priority_1" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white text-slate-700">
                                    <option value="admob" {{ ($settings['ad_priority_1'] ?? 'admob') == 'admob' ? 'selected' : '' }}>Google AdMob</option>
                                    <option value="facebook" {{ ($settings['ad_priority_1'] ?? '') == 'facebook' ? 'selected' : '' }}>Facebook Audience Network</option>
                                    <option value="unity" {{ ($settings['ad_priority_1'] ?? '') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                                    <option value="none" {{ ($settings['ad_priority_1'] ?? '') == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>

                            <!-- 2nd Priority -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">2nd Priority (Fallback)</label>
                                <select name="ad_priority_2" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white text-slate-700">
                                    <option value="admob" {{ ($settings['ad_priority_2'] ?? '') == 'admob' ? 'selected' : '' }}>Google AdMob</option>
                                    <option value="facebook" {{ ($settings['ad_priority_2'] ?? 'facebook') == 'facebook' ? 'selected' : '' }}>Facebook Audience Network</option>
                                    <option value="unity" {{ ($settings['ad_priority_2'] ?? '') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                                    <option value="none" {{ ($settings['ad_priority_2'] ?? '') == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>

                            <!-- 3rd Priority -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">3rd Priority (Last Resort)</label>
                                <select name="ad_priority_3" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all bg-white text-slate-700">
                                    <option value="admob" {{ ($settings['ad_priority_3'] ?? '') == 'admob' ? 'selected' : '' }}>Google AdMob</option>
                                    <option value="facebook" {{ ($settings['ad_priority_3'] ?? '') == 'facebook' ? 'selected' : '' }}>Facebook Audience Network</option>
                                    <option value="unity" {{ ($settings['ad_priority_3'] ?? 'unity') == 'unity' ? 'selected' : '' }}>Unity Ads</option>
                                    <option value="none" {{ ($settings['ad_priority_3'] ?? '') == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-slate-100">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Save Ad Configuration
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Day</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Coins Reward</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Gems Reward</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach($rewards as $reward)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <form action="{{ route('admin.daily_rewards.update', $reward->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-sm border border-indigo-100">
                                        {{ $reward->day }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="relative rounded-xl shadow-sm max-w-xs">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-coins text-amber-500"></i>
                                    </div>
                                    <input type="number" name="coins" value="{{ $reward->coins }}" min="0" required
                                        class="block w-32 pl-10 pr-3 py-2 text-sm border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-slate-700">
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="relative rounded-xl shadow-sm max-w-xs">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-gem text-indigo-500"></i>
                                    </div>
                                    <input type="number" name="gems" value="{{ $reward->gems }}" min="0" required
                                        class="block w-32 pl-10 pr-3 py-2 text-sm border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-slate-700">
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm shadow-indigo-200">
                                    Update
                                </button>
                            </td>
                        </form>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
