<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ \App\Models\Setting::get('app_name', config('app.name')) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen w-full flex items-center justify-center px-4 relative overflow-hidden">
    <!-- Background Decoration -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-indigo-600 to-slate-50 -z-10"></div>
    <div class="absolute top-10 left-10 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl"></div>
    <div class="absolute top-20 right-20 w-48 h-48 bg-indigo-400 opacity-10 rounded-full blur-3xl"></div>

    <div class="w-full max-w-sm bg-white rounded-xl shadow-xl border border-slate-100 p-8 z-10">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-500/30 text-white">
                <i class="fas fa-layer-group text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800">{{ \App\Models\Setting::get('app_name', config('app.name')) }}</h2>
            <p class="text-slate-500 text-xs mt-1 font-medium">Please sign in to continue</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-100 text-rose-600 p-3 rounded-lg mb-6 flex items-start text-xs">
                <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                <div>
                    <span class="font-bold">Error:</span> {{ $errors->first() }}
                </div>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wide">Email Address</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-slate-400 text-sm group-focus-within:text-indigo-600 transition-colors"></i>
                    </div>
                    <input type="email" name="email" id="email"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200"
                           placeholder="admin@example.com" required autofocus value="{{ old('email') }}">
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wide">Password</label>
                </div>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-slate-400 text-sm group-focus-within:text-indigo-600 transition-colors"></i>
                    </div>
                    <input type="password" name="password" id="password"
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200"
                           placeholder="••••••••" required>
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded cursor-pointer">
                    <label for="remember_me" class="ml-2 block text-xs text-slate-600 cursor-pointer font-medium">Remember me</label>
                </div>
                <a href="#" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors">Forgot password?</a>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg shadow-indigo-500/20 text-sm">
                Sign In
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-[10px] text-slate-400 font-medium">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('app_name', config('app.name')) }}. Secure Admin Access.
            </p>
        </div>
    </div>
    <script>
        const toggle = document.getElementById('togglePassword');
        const input = document.getElementById('password');
        if (toggle && input) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent focus loss or form submission quirks
                const is = input.getAttribute('type') === 'password';
                input.setAttribute('type', is ? 'text' : 'password');
                const icon = this.querySelector('i');
                if(is) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
    </script>
</body>
</html>
