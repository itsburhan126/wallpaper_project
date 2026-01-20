@extends('layouts.admin')

@section('header', 'Add Short')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Short</h1>
            <p class="text-sm text-slate-500 mt-1">Upload a new short video</p>
        </div>
        <a href="{{ route('admin.shorts.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all text-sm font-medium flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left"></i> Back to Shorts
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.shorts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
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

                <div class="grid grid-cols-1 gap-8">
                    <!-- Title Input -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Short Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}"
                            class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm"
                            placeholder="e.g. Amazing Wallpaper Transformation">
                    </div>

                    <!-- Video Upload -->
                    <div>
                        <label for="video" class="block text-sm font-semibold text-slate-700 mb-2">Video File (MP4) <span class="text-red-500">*</span></label>
                        <div class="relative border-2 border-dashed border-slate-300 rounded-lg p-8 hover:bg-slate-50 transition-colors text-center cursor-pointer" onclick="document.getElementById('video').click()">
                            <input type="file" name="video" id="video" accept="video/mp4" required class="hidden" onchange="showFileName(this, 'video-name')">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-400 mb-3"></i>
                            <p class="text-sm font-medium text-slate-700">Click to upload video</p>
                            <p class="text-xs text-slate-500 mt-1">MP4 format, Max 100MB</p>
                            <p id="video-name" class="text-sm text-indigo-600 mt-2 font-medium"></p>
                        </div>
                    </div>

                    <!-- Thumbnail Upload -->
                    <div>
                        <label for="thumbnail" class="block text-sm font-semibold text-slate-700 mb-2">Thumbnail Image <span class="text-gray-400 text-xs font-normal">(Optional)</span></label>
                        <div class="relative border-2 border-dashed border-slate-300 rounded-lg p-8 hover:bg-slate-50 transition-colors text-center cursor-pointer" onclick="document.getElementById('thumbnail').click()">
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="hidden" onchange="showFileName(this, 'thumbnail-name')">
                            <i class="fas fa-image text-4xl text-slate-400 mb-3"></i>
                            <p class="text-sm font-medium text-slate-700">Click to upload thumbnail</p>
                            <p class="text-xs text-slate-500 mt-1">JPG, PNG format</p>
                            <p id="thumbnail-name" class="text-sm text-indigo-600 mt-2 font-medium"></p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all">
                        <label for="is_active" class="text-sm font-medium text-slate-700">Active (Visible in App)</label>
                    </div>

                </div>
            </div>
            
            <div class="bg-slate-50 px-8 py-4 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all text-sm font-medium shadow-md shadow-indigo-500/20 flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Short
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function showFileName(input, targetId) {
        const fileName = input.files[0]?.name;
        if (fileName) {
            document.getElementById(targetId).textContent = "Selected: " + fileName;
        }
    }
</script>
@endsection