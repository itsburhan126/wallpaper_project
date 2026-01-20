@extends('layouts.admin')

@section('header', 'Referral Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.referral.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Referral & Bonus Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Referral & Signup Bonuses</h3>
                        <p class="text-sm text-gray-500">Configure rewards for new users and referrals</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Signup Bonus (Gems)</label>
                        <div class="relative">
                            <input type="number" name="signup_bonus" value="{{ $settings['signup_bonus'] ?? '100' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all pl-10">
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-gem"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Gems given to a new user upon registration</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level 1 Referral Bonus (Gems)</label>
                        <div class="relative">
                            <input type="number" name="referral_bonus_l1" value="{{ $settings['referral_bonus_l1'] ?? '50' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all pl-10">
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Reward for direct referrer</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level 2 Referral Bonus (Gems)</label>
                        <div class="relative">
                            <input type="number" name="referral_bonus_l2" value="{{ $settings['referral_bonus_l2'] ?? '20' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all pl-10">
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Reward for referrer's referrer</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level 3 Referral Bonus (Gems)</label>
                        <div class="relative">
                            <input type="number" name="referral_bonus_l3" value="{{ $settings['referral_bonus_l3'] ?? '10' }}" 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all pl-10">
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-network-wired"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Reward for 3rd level referrer</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-yellow-600 to-orange-600 text-white font-bold rounded-xl shadow-lg shadow-yellow-500/30 hover:shadow-yellow-500/50 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
