@extends('layouts.admin')

@section('header', 'Edit Wallpaper')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Wallpaper</h1>
            <p class="text-sm text-slate-500 mt-1">Update wallpaper details</p>
        </div>
        <a href="{{ route('admin.wallpapers.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all text-sm font-medium flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left"></i> Back to Wallpapers
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.wallpapers.update', $wallpaper->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-6 flex items-start gap-3">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <div>
                            <h4 class="font-semibold text-sm">Please fix the following errors:</h4>
                            <ul class="list-disc list-inside text-sm mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="space-y-6">
                    <!-- Category Select -->
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="category_id" id="category_id" required
                                class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm appearance-none">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $wallpaper->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label for="image" class="block text-sm font-semibold text-slate-700 mb-2">Wallpaper Image</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-lg p-8 text-center hover:bg-slate-50 transition-colors relative">
                            <input type="file" name="image" id="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                            <div id="upload-placeholder" class="space-y-2 {{ $wallpaper->thumbnail ? 'hidden' : '' }}">
                                <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-cloud-upload-alt text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-700">Click to upload or drag and drop</p>
                                <p class="text-xs text-slate-400">Leave empty to keep current image</p>
                            </div>
                            <img id="image-preview" src="{{ $wallpaper->thumbnail ? asset('storage/' . $wallpaper->thumbnail) : '#' }}" alt="Preview" class="{{ $wallpaper->thumbnail ? '' : 'hidden' }} max-h-64 mx-auto rounded-lg shadow-sm">
                        </div>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label for="tags" class="block text-sm font-semibold text-slate-700 mb-2">Tags</label>
                        <input type="text" name="tags" id="tags" value="{{ old('tags', $wallpaper->tags) }}"
                            class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm"
                            placeholder="nature, abstract, dark (comma separated)">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all text-sm font-medium shadow-sm shadow-indigo-500/20">
                        Update Wallpaper
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        const placeholder = document.getElementById('upload-placeholder');
        const preview = document.getElementById('image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
