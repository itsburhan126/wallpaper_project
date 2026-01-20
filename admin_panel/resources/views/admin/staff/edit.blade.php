@extends('layouts.admin')

@section('header', 'Edit Staff Member')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Edit Staff: {{ $staff->name }}</h2>
                    <p class="text-sm text-gray-500">Update staff details and role assignment.</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $staff->name) }}" required 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $staff->email) }}" required 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-gray-400 font-normal">(Leave blank to keep current)</span></label>
                        <input type="password" name="password" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                            placeholder="••••••••">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                            placeholder="••••••••">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Role</label>
                        <select name="role_id" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all appearance-none">
                            <option value="">Select a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $staff->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('admin.staff.index') }}" class="px-6 py-2.5 rounded-xl text-gray-600 hover:bg-gray-100 font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all">Update Staff Member</button>
                </div>
            </div>
        </form>
    </div>
@endsection
