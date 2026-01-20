@extends('layouts.admin')

@section('header', 'Create Banner')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Create Banner</h1>
            <p class="text-slate-500 text-sm mt-1">Upload a banner image for the app home page</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Title (Optional)</label>
                    <input type="text" name="title" id="title"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-slate-400"
                        placeholder="e.g. Summer Sale">
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Banner Image <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg hover:border-indigo-500 transition-colors relative" id="drop-zone">
                        <div class="space-y-1 text-center" id="upload-prompt">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-3"></i>
                            <div class="flex text-sm text-slate-600 justify-center">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*" required onchange="previewImage(event)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500">Recommended size: 1200x600px</p>
                        </div>
                        <div id="image-preview" class="hidden absolute inset-0 flex items-center justify-center bg-white rounded-lg">
                            <img src="" class="max-h-full max-w-full rounded-lg object-contain p-2">
                            <button type="button" onclick="removeImage()" class="absolute top-2 right-2 p-1 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <div class="flex items-center h-5">
                        <input id="status" name="status" type="checkbox" value="1" checked 
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
                    Create Banner
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
        document.getElementById('upload-prompt').classList.remove('hidden');
        document.getElementById('drop-zone').classList.add('border-dashed');
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('image-preview').querySelector('img').src = '';
    }
</script>
@endsection
