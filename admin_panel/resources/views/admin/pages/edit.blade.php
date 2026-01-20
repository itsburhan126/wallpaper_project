@extends('layouts.admin')

@section('title')
    Edit Page: {{ $page->title }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.pages.index') }}" class="text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Pages
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h1 class="text-xl font-bold text-slate-800">Edit Page Content</h1>
                <p class="text-sm text-slate-500 mt-1">Update the content for {{ $page->title }}</p>
            </div>
            
            <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Page Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-slate-700" required>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-slate-700 mb-1">Content</label>
                    <div class="prose max-w-none">
                        <textarea name="content" id="summernote" class="w-full rounded-lg border border-slate-200">{{ old('content', $page->content) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-100">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm shadow-indigo-200 transition-all font-medium transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Summernote CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $('#summernote').summernote({
        placeholder: 'Write page content here...',
        tabsize: 2,
        height: 400,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
</script>
@endsection
