@extends('layouts.admin')

@section('title', 'Create New Role')

@section('content')
<div class="container-fluid">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.roles.index') }}" class="text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Roles
            </a>
        </div>

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Role Details</h2>
                    <p class="text-sm text-slate-500">Define the role name and assign permissions.</p>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Role Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                            class="w-full px-4 py-3 rounded-lg bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all text-slate-700 placeholder-slate-400"
                            placeholder="e.g. Editor, Moderator, Support Manager">
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-4">Permissions</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($permissions as $key => $label)
                            <label class="relative flex items-start p-4 rounded-lg border border-slate-200 cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition-all group">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-slate-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-medium text-slate-900 group-hover:text-indigo-700">{{ $label }}</span>
                                    <p class="text-slate-500 text-xs mt-0.5">Allows access to {{ strtolower(str_replace('Manage ', '', $label)) }} module.</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('admin.roles.index') }}" class="px-6 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-all">Create Role</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
