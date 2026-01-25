@extends('layouts.admin')

@section('header', 'Edit Wallpaper')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Wallpaper</h1>
            <p class="text-slate-500 text-sm mt-1">Update wallpaper details</p>
        </div>
        <a href="{{ route('admin.wallpapers.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.wallpapers.update', $wallpaper->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-900">Wallpaper Details</h2>
                <p class="text-sm text-slate-500">Update the details for this wallpaper.</p>
            </div>
            <div class="p-6 grid grid-cols-1 gap-6">
                <!-- Current Image Preview -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Current Image</label>
                    <div class="w-64 h-40 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 shadow-sm relative group">
                        <img src="{{ asset('storage/' . $wallpaper->image) }}" alt="Current Wallpaper" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Change Image (Optional)</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-indigo-50/30 hover:border-indigo-300 transition-all group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-indigo-500"></i>
                                </div>
                                <p class="text-sm text-slate-500 font-medium">Click to upload new image</p>
                            </div>
                            <input id="image" name="image" type="file" class="hidden" accept="image/*" />
                        </label>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-500 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-bold text-slate-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="category_id" id="category_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all appearance-none bg-slate-50 font-medium text-slate-700">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id') ?? $wallpaper->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-500 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-bold text-slate-700 mb-2">Tags</label>
                    <input type="text" name="tags" id="tags" value="{{ old('tags') ?? $wallpaper->tags }}"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all placeholder-slate-400 bg-slate-50 font-medium text-slate-700"
                        placeholder="nature, abstract, dark (comma separated)">
                    <p class="text-xs text-slate-400 mt-2">Separate tags with commas for better searchability.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Update Wallpaper</span>
            </button>
        </div>
    </form>
</div>
@endsection
