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
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-900">Short Details</h2>
                <p class="text-sm text-slate-500">Upload a new video short.</p>
            </div>
            
            <div class="p-8">
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl relative mb-6 flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-sm">Important: Video Resolution</h4>
                        <p class="text-sm mt-1">Please ensure uploaded videos are <strong>1080p (1920x1080)</strong> or lower. 4K videos (3840x2160) may not play on many mobile devices due to hardware limitations.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl relative mb-6 flex items-start gap-3">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <div>
                            <h4 class="font-bold text-sm">Please fix the following errors:</h4>
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
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Short Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-sm font-medium text-slate-700 placeholder-slate-400"
                            placeholder="e.g. Amazing Wallpaper Transformation">
                    </div>

                    <!-- Video Upload -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Video Source <span class="text-red-500">*</span></label>
                        
                        <!-- Tabs for Video Source -->
                        <div class="flex gap-4 mb-4" x-data="{ activeTab: 'upload' }">
                            <button type="button" onclick="document.getElementById('upload-section').classList.remove('hidden'); document.getElementById('youtube-section').classList.add('hidden'); document.getElementById('video').required = true; document.getElementById('youtube_url').required = false; document.getElementById('youtube_url').value = '';" 
                                class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-bold hover:bg-indigo-100 transition-colors">
                                <i class="fas fa-cloud-upload-alt mr-2"></i> Upload File
                            </button>
                            <button type="button" onclick="document.getElementById('upload-section').classList.add('hidden'); document.getElementById('youtube-section').classList.remove('hidden'); document.getElementById('video').required = false; document.getElementById('video').value = ''; document.getElementById('youtube_url').required = true;"
                                class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-bold hover:bg-red-100 transition-colors">
                                <i class="fab fa-youtube mr-2"></i> YouTube URL
                            </button>
                        </div>

                        <!-- Upload Section -->
                        <div id="upload-section">
                            <label for="video" class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Upload Video File</label>
                            <div class="relative border-2 border-dashed border-slate-300 rounded-xl p-8 hover:bg-indigo-50/30 hover:border-indigo-300 transition-all text-center cursor-pointer group" onclick="document.getElementById('video').click()">
                                <input type="file" name="video" id="video" accept="video/mp4" required class="hidden" onchange="showFileName(this, 'video-name')">
                                <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-indigo-500"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-700">Click to upload video</p>
                                <p class="text-xs text-slate-500 mt-1">MP4 format, Max 100MB. <strong>Max Resolution: 1080p</strong></p>
                                <p id="video-name" class="text-sm text-indigo-600 mt-2 font-medium"></p>
                            </div>
                        </div>

                        <!-- YouTube Section -->
                        <div id="youtube-section" class="hidden">
                            <label for="youtube_url" class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">YouTube Short URL</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fab fa-youtube text-red-500 text-lg"></i>
                                </div>
                                <input type="url" name="youtube_url" id="youtube_url" 
                                    class="w-full pl-10 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all text-sm font-medium text-slate-700 placeholder-slate-400"
                                    placeholder="https://youtube.com/shorts/...">
                            </div>
                            <p class="text-xs text-slate-500 mt-2">Paste the full YouTube Short URL here.</p>
                        </div>
                    </div>

                    <!-- Thumbnail Upload -->
                    <div>
                        <label for="thumbnail" class="block text-sm font-bold text-slate-700 mb-2">Thumbnail Image <span class="text-slate-400 text-xs font-normal">(Optional)</span></label>
                        <div class="relative border-2 border-dashed border-slate-300 rounded-xl p-8 hover:bg-indigo-50/30 hover:border-indigo-300 transition-all text-center cursor-pointer group" onclick="document.getElementById('thumbnail').click()">
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="hidden" onchange="showFileName(this, 'thumbnail-name')">
                            <div class="w-16 h-16 rounded-full bg-purple-50 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-image text-3xl text-purple-500"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700">Click to upload thumbnail</p>
                            <p class="text-xs text-slate-500 mt-1">JPG, PNG format</p>
                            <p id="thumbnail-name" class="text-sm text-indigo-600 mt-2 font-medium"></p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                        <label for="is_active" class="text-sm font-bold text-slate-700 cursor-pointer select-none">Active (Visible in App)</label>
                    </div>

                </div>
            </div>
            
            <div class="bg-slate-50 px-8 py-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Save Short</span>
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