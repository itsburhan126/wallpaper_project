@extends('layouts.admin')

@section('header', 'Add Game')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Game</h1>
            <p class="text-sm text-slate-500 mt-1">Create a new game and set up rewards</p>
        </div>
        <a href="{{ route('admin.games.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all text-sm font-medium flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left"></i> Back to Games
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Title Input -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Game Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}"
                            class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm"
                            placeholder="e.g. Bubble Shooter">
                    </div>

                    <!-- URL Input -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="url" class="block text-sm font-semibold text-slate-700 mb-2">Game URL <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-link absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="url" name="url" id="url" required value="{{ old('url') }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm"
                                placeholder="https://example.com/game">
                        </div>
                    </div>

                    <!-- Play Time -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="play_time" class="block text-sm font-semibold text-slate-700 mb-2">Play Time (Seconds) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="number" name="play_time" id="play_time" required min="1" value="{{ old('play_time', 60) }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">Time user must play to get reward.</p>
                    </div>

                    <!-- Win Reward -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="win_reward" class="block text-sm font-semibold text-slate-700 mb-2">Win Reward (Coins) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-coins absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="number" name="win_reward" id="win_reward" required min="0" value="{{ old('win_reward', 10) }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all text-sm resize-none" placeholder="Enter game description...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Image Upload -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Game Image <span class="text-red-500">*</span></label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:border-indigo-500 hover:bg-indigo-50/50 transition-all cursor-pointer relative group" id="drop-zone">
                            <input type="file" name="image" id="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" required onchange="previewImage(event)">
                            <div id="upload-prompt" class="group-hover:scale-105 transition-transform duration-300">
                                <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-indigo-100 transition-colors">
                                    <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                </div>
                                <p class="text-slate-700 font-medium">Click to upload or drag and drop</p>
                                <p class="text-xs text-slate-400 mt-1">SVG, PNG, JPG or GIF (max. 2MB)</p>
                            </div>
                            <div id="image-preview" class="hidden h-48 flex items-center justify-center relative z-20 pointer-events-none">
                                <img src="" class="max-h-full rounded-lg shadow-sm object-contain">
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-span-2 flex flex-col sm:flex-row gap-6 p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked 
                                class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            <label for="is_active" class="ml-2 block text-sm font-medium text-slate-700 cursor-pointer select-none">
                                Active (Show in app)
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1" 
                                class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-500">
                            <label for="is_featured" class="ml-2 block text-sm font-medium text-slate-700 cursor-pointer select-none">
                                Featured (Show on Home)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-8 py-5 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('admin.games.index') }}" class="px-5 py-2.5 rounded-lg text-slate-600 font-medium hover:bg-white hover:text-slate-800 border border-transparent hover:border-slate-200 transition-all text-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all text-sm flex items-center gap-2">
                    <i class="fas fa-save"></i> Create Game
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
                const preview = document.getElementById('image-preview');
                preview.classList.remove('hidden');
                preview.querySelector('img').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
