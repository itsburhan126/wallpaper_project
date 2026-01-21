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
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Current Image Preview -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Current Image</label>
                    <div class="w-64 h-40 rounded-lg overflow-hidden border border-slate-200 bg-slate-50">
                        <img src="{{ asset('storage/' . $wallpaper->image) }}" alt="Current Wallpaper" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Change Image (Optional)</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-3xl text-slate-400 mb-2"></i>
                                <p class="text-sm text-slate-500">Click to upload new image</p>
                            </div>
                            <input id="image" name="image" type="file" class="hidden" accept="image/*" />
                        </label>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id') ?? $wallpaper->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-medium text-slate-700 mb-1">Tags</label>
                    <input type="text" name="tags" id="tags" value="{{ old('tags') ?? $wallpaper->tags }}"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-slate-400"
                        placeholder="nature, abstract, dark (comma separated)">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm shadow-indigo-200">
                Update Wallpaper
            </button>
        </div>
    </form>
</div>
@endsection
