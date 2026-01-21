@extends('layouts.admin')

@section('header', 'Add Wallpaper')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Wallpaper</h1>
            <p class="text-slate-500 text-sm mt-1">Upload a new wallpaper to the collection</p>
        </div>
        <a href="{{ route('admin.wallpapers.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.wallpapers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Wallpaper Image <span class="text-red-500">*</span></label>
                    <div class="flex items-center justify-center w-full">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-64 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-4xl text-slate-400 mb-3"></i>
                                <p class="text-sm text-slate-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-slate-500 mt-1">PNG, JPG or GIF (MAX. 2MB)</p>
                            </div>
                            <input id="image" name="image" type="file" class="hidden" required accept="image/*" />
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
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                    <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-slate-400"
                        placeholder="nature, abstract, dark (comma separated)">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm shadow-indigo-200">
                Upload Wallpaper
            </button>
        </div>
    </form>
</div>
@endsection
