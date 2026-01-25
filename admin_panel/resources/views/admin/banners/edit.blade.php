@extends('layouts.admin')

@section('header', 'Edit Banner')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Banner</h1>
            <p class="text-slate-500 text-sm mt-1">Update banner details</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Title (Optional)</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $banner->title) }}"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-slate-400"
                        placeholder="e.g. Summer Sale">
                </div>

                <!-- Link -->
                <div>
                    <label for="link" class="block text-sm font-medium text-slate-700 mb-1">URL (Optional)</label>
                    <input type="url" name="link" id="link" value="{{ old('link', $banner->link) }}"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-slate-400"
                        placeholder="e.g. https://google.com">
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Banner Image</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg hover:border-indigo-500 transition-colors relative {{ $banner->image ? 'border-indigo-500' : '' }}" id="drop-zone">
                        <div class="space-y-1 text-center {{ $banner->image ? 'hidden' : '' }}" id="upload-prompt">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-3"></i>
                            <div class="flex text-sm text-slate-600 justify-center">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(event)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500">Recommended size: 1200x600px</p>
                        </div>
                        <div id="image-preview" class="{{ $banner->image ? '' : 'hidden' }} absolute inset-0 flex items-center justify-center bg-white rounded-lg">
                            <img src="{{ $banner->image ? \App\Helpers\FilePath::getUrl($banner->image) : '' }}" class="max-h-full max-w-full rounded-lg object-contain p-2">
                            <button type="button" onclick="removeImage()" class="absolute top-2 right-2 p-1 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Leave empty to keep current image</p>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <div class="flex items-center h-5">
                        <input id="status" name="status" type="checkbox" value="1" {{ old('status', $banner->status) ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded cursor-pointer">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="status" class="font-medium text-slate-700 cursor-pointer">Active</label>
                        <p class="text-slate-500">Show this banner in the app</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors text-sm font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors text-sm font-medium shadow-sm shadow-indigo-200">
                    Update Banner
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('upload-prompt').classList.add('hidden');
                document.getElementById('drop-zone').classList.remove('border-dashed');
                const preview = document.getElementById('image-preview');
                preview.classList.remove('hidden');
                preview.querySelector('img').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }

    function removeImage() {
        document.getElementById('image').value = '';
        // If there was an existing image, show the upload prompt instead
        // This logic is slightly complex because we might want to keep the original image if the user cancels upload
        // For simplicity, we'll just clear the preview and show upload prompt. 
        // Note: Backend handles "nullable" image, so if no file is sent, it keeps the old one unless we explicitly want to delete it.
        // But here we are just resetting the *new* file input.
        // Visual feedback for "clearing the current image" isn't fully implemented in this simple script 
        // (usually you'd have a hidden field to flag deletion), but typically "Update" without file means "keep old".
        
        document.getElementById('upload-prompt').classList.remove('hidden');
        document.getElementById('drop-zone').classList.add('border-dashed');
        document.getElementById('image-preview').classList.add('hidden');
        document.querySelector('#image-preview img').src = '';
    }
</script>
@endsection