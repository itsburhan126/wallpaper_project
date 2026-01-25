@extends('layouts.admin')

@section('header', 'My Profile')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                            <i class="fas fa-user-circle text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Profile Information</h2>
                            <p class="text-sm text-slate-500">Update your account's profile information and email address.</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-800">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-800">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                            <i class="fas fa-lock text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Update Password</h2>
                            <p class="text-sm text-slate-500">Ensure your account is using a long, random password to stay secure.</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Current Password</label>
                        <div class="relative">
                            <input type="password" name="current_password" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-800">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-key"></i>
                            </div>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" name="password" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-800">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-800">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="submit" class="px-8 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
