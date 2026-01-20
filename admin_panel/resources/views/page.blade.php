<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - {{ config('app.name', 'Game Project') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .prose h1 { font-size: 2.25em; margin-top: 0; margin-bottom: 0.8em; line-height: 1.1111111; font-weight: 800; color: #111827; }
        .prose h2 { font-size: 1.5em; margin-top: 2em; margin-bottom: 1em; line-height: 1.3333333; font-weight: 700; color: #111827; }
        .prose h3 { font-size: 1.25em; margin-top: 1.6em; margin-bottom: 0.6em; line-height: 1.6; font-weight: 600; color: #111827; }
        .prose p { margin-top: 1.25em; margin-bottom: 1.25em; line-height: 1.75; color: #374151; }
        .prose ul { margin-top: 1.25em; margin-bottom: 1.25em; list-style-type: disc; padding-left: 1.625em; }
        .prose li { margin-top: 0.5em; margin-bottom: 0.5em; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Main Content -->
    <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8">
        <article class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Title Header -->
            <div class="bg-gradient-to-r from-indigo-50 to-white px-8 py-10 border-b border-gray-100">
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ $page->title }}
                </h1>
                <div class="mt-4 flex items-center gap-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <i class="far fa-calendar-alt"></i>
                        Last updated: {{ $page->updated_at->format('M d, Y') }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-10">
                <div class="prose max-w-none">
                    {!! $page->content !!}
                </div>
            </div>
        </article>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 mt-auto">
        <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Game Project') }}. All rights reserved.
                </p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Facebook</span>
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Twitter</span>
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">GitHub</span>
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
