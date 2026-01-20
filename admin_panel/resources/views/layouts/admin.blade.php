<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ \App\Models\Setting::get('app_name', config('app.name')) }}</title>
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
        }

        .nav-item {
            position: relative;
            transition: all 0.2s ease-in-out;
        }

        .nav-item-active {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-weight: 600;
        }
        
        .nav-item-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 24px;
            width: 4px;
            background-color: #6366f1; /* Primary-500 */
            border-radius: 0 4px 4px 0;
        }

        .nav-item:hover:not(.nav-item-active) {
            background: rgba(255, 255, 255, 0.05);
            color: #f8fafc;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent; 
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
        
        .content-transition {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
    @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('demo-admin'))
    <div class="bg-amber-500 text-white text-xs font-bold text-center py-1 fixed top-0 left-0 right-0 z-50">
        <i class="fas fa-exclamation-triangle mr-1"></i> DEMO MODE: View Only - Changes will not be saved.
    </div>
    @endif
    
    <div id="mobile-overlay" class="fixed inset-0 bg-slate-900/50 z-20 hidden transition-opacity opacity-0 md:hidden backdrop-blur-sm"></div>
    
    <div class="flex h-screen overflow-hidden {{ (Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('demo-admin')) ? 'pt-6' : '' }}">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-72 sidebar-gradient flex-shrink-0 flex flex-col z-30 shadow-2xl transition-all duration-300 fixed md:static inset-y-0 left-0 transform -translate-x-full md:translate-x-0 border-r border-slate-700/50">
            <!-- Brand -->
            <div class="h-20 flex items-center px-8 border-b border-slate-700/50">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 text-white">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg text-white tracking-tight leading-tight">{{ \App\Models\Setting::get('app_name', config('app.name')) }}</h1>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Admin Console</p>
                    </div>
                </div>
                <!-- Close Button (Mobile Only) -->
                <button id="mobile-menu-close" class="md:hidden ml-auto text-slate-400 hover:text-white transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
            <div id="sidebar-nav" class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                <p class="px-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Dashboard</p>
                
                @if(Auth::guard('admin')->user()->hasPermission('dashboard_access'))
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-home w-6 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Overview</span>
                </a>
                @endif

                <a href="{{ route('admin.profile.edit') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.profile.edit') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-user-circle w-6 {{ request()->routeIs('admin.profile.edit') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>My Profile</span>
                </a>

                <p class="px-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-8 mb-3">User Management</p>
                
                <a href="{{ route('admin.users.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.users.*') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-users w-6 {{ request()->routeIs('admin.users.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>All Users</span>
                </a>

                @if(Auth::guard('admin')->user()->hasPermission('manage_staff'))
                <p class="px-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-8 mb-3">Access Control</p>

                <a href="{{ route('admin.staff.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.staff.*') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-user-shield w-6 {{ request()->routeIs('admin.staff.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Staff Members</span>
                </a>

                <a href="{{ route('admin.roles.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.roles.*') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-key w-6 {{ request()->routeIs('admin.roles.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Roles & Permissions</span>
                </a>
                @endif

                <a href="{{ route('admin.pages.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.pages.*') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-file-alt w-6 {{ request()->routeIs('admin.pages.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Content Pages</span>
                </a>

                @if(Auth::guard('admin')->user()->hasPermission('manage_settings'))
                <p class="px-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-8 mb-3">Game Center</p>

                <a href="{{ route('admin.categories.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.categories.*') ? 'nav-item-active' : 'text-slate-400' }}">
                    <div class="w-6 flex justify-center mr-0">
                        <i class="fas fa-th-large {{ request()->routeIs('admin.categories.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    </div>
                    <span>Categories</span>
                </a>

                <a href="{{ route('admin.wallpapers.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.wallpapers.*') ? 'nav-item-active' : 'text-slate-400' }}">
                    <div class="w-6 flex justify-center mr-0">
                        <i class="fas fa-images {{ request()->routeIs('admin.wallpapers.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    </div>
                    <span>Wallpapers</span>
                </a>

                <a href="{{ route('admin.games.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.games.*') ? 'nav-item-active' : 'text-slate-400' }}">
                     <div class="w-6 flex justify-center mr-0">
                        <i class="fas fa-gamepad {{ request()->routeIs('admin.games.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    </div>
                    <span>Games Library</span>
                </a>

                <a href="{{ route('admin.daily_rewards.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.daily_rewards.*') ? 'nav-item-active' : 'text-slate-400' }}">
                     <div class="w-6 flex justify-center mr-0">
                        <i class="fas fa-gift {{ request()->routeIs('admin.daily_rewards.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    </div>
                    <span>Daily Rewards</span>
                </a>

                <a href="{{ route('admin.settings.game') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.settings.game') ? 'nav-item-active' : 'text-slate-400' }}">
                     <div class="w-6 flex justify-center mr-0">
                        <i class="fas fa-chess-board {{ request()->routeIs('admin.settings.game') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    </div>
                    <span>Game Settings</span>
                </a>

                <p class="px-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-8 mb-3">System Configuration</p>
                
                <a href="{{ route('admin.redeem.requests.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.redeem.requests.*') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-receipt w-6 {{ request()->routeIs('admin.redeem.requests.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Withdrawal Requests</span>
                </a>

                <a href="{{ route('admin.redeem.gateways.index') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.redeem.*') && !request()->routeIs('admin.redeem.requests.*') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-wallet w-6 {{ request()->routeIs('admin.redeem.*') && !request()->routeIs('admin.redeem.requests.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Payment Gateways</span>
                </a>

                <a href="{{ route('admin.settings.general') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.settings.general') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-sliders-h w-6 {{ request()->routeIs('admin.settings.general') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>General Settings</span>
                </a>

                <a href="{{ route('admin.settings.ads') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.settings.ads') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-ad w-6 {{ request()->routeIs('admin.settings.ads') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Ad Networks</span>
                </a>

                <a href="{{ route('admin.settings.api') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.settings.api') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-server w-6 {{ request()->routeIs('admin.settings.api') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>API Keys</span>
                </a>

                <a href="{{ route('admin.settings.email') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.settings.email') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-envelope-open-text w-6 {{ request()->routeIs('admin.settings.email') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>SMTP Settings</span>
                </a>

                <a href="{{ route('admin.settings.referral') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.settings.referral') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-users-cog w-6 {{ request()->routeIs('admin.settings.referral') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Referral System</span>
                </a>

                <a href="{{ route('admin.settings.deep_link') }}" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg group {{ request()->routeIs('admin.settings.deep_link') ? 'nav-item-active' : 'text-slate-400' }}">
                    <i class="fas fa-link w-6 {{ request()->routeIs('admin.settings.deep_link') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span>Deep Links</span>
                </a>
                @endif
            </div>

            <!-- User Profile (Bottom) -->
            <div class="p-4 border-t border-slate-700/50 bg-slate-900/30">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold shadow-md border border-slate-600">
                        {{ substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::guard('admin')->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ Auth::guard('admin')->user()->email ?? '' }}</p>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-red-400 transition-colors rounded-lg hover:bg-white/5" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50 relative">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 sticky top-0 shadow-sm">
                <!-- Mobile Toggle -->
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-btn" class="text-slate-500 hover:text-slate-800 focus:outline-none p-2 rounded-md hover:bg-slate-100">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="ml-3 font-bold text-slate-800 text-lg">{{ \App\Models\Setting::get('app_name', config('app.name')) }}</span>
                </div>

                <!-- Page Title / Breadcrumb -->
                <div class="hidden md:flex flex-col justify-center">
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight leading-none">
                        @yield('header')
                    </h2>
                    <div class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                        <span>Admin</span>
                        <i class="fas fa-chevron-right text-[8px]"></i>
                        <span class="text-indigo-500 font-medium">@yield('header')</span>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <!-- Notifications (Placeholder) -->
                    <button class="relative p-2 text-slate-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-indigo-50">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                    </button>

                    <!-- Search -->
                    <div class="hidden md:block relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 rounded-lg bg-slate-100 border-none focus:ring-2 focus:ring-indigo-500/20 focus:bg-white text-sm w-64 transition-all placeholder-slate-400 text-slate-700">
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6 lg:p-8 scroll-smooth">
                <div class="max-w-7xl mx-auto content-transition">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-6 flex items-center p-4 bg-emerald-50 rounded-lg border border-emerald-100 shadow-sm">
                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-semibold text-emerald-800">Success</h3>
                                <div class="text-sm text-emerald-600">{{ session('success') }}</div>
                            </div>
                            <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 flex items-start p-4 bg-rose-50 rounded-lg border border-rose-100 shadow-sm">
                            <div class="flex-shrink-0 w-8 h-8 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 mt-0.5">
                                <i class="fas fa-exclamation text-sm"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-semibold text-rose-800">Something went wrong</h3>
                                <ul class="list-disc list-inside text-sm text-rose-600 mt-1 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button onclick="this.parentElement.remove()" class="ml-auto text-rose-400 hover:text-rose-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
                
                <!-- Footer -->
                <div class="mt-auto pt-6 border-t border-slate-200/60 text-center text-xs text-slate-400 pb-8">
                    &copy; {{ date('Y') }} <span class="font-medium text-slate-500">{{ \App\Models\Setting::get('app_name', 'Admin Panel') }}</span>. All rights reserved.
                </div>
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Toggle
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('mobile-menu-close');
            const sidebarEl = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            if (mobileBtn && sidebarEl && overlay) {
                function toggleMenu() {
                    const isClosed = sidebarEl.classList.contains('-translate-x-full');
                    
                    if (isClosed) {
                        sidebarEl.classList.remove('-translate-x-full');
                        overlay.classList.remove('hidden');
                        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                    } else {
                        sidebarEl.classList.add('-translate-x-full');
                        overlay.classList.add('opacity-0');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    }
                }

                mobileBtn.addEventListener('click', toggleMenu);
                if (closeBtn) closeBtn.addEventListener('click', toggleMenu);
                overlay.addEventListener('click', toggleMenu);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
