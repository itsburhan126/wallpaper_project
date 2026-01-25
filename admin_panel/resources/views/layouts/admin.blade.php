<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - {{ \App\Models\Setting::get('app_name', config('app.name')) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        slate: {
                            850: '#151f32', // Custom dark slate
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02)',
                        'glow': '0 0 15px rgba(99, 102, 241, 0.3)',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Sidebar Styling - White Theme */
        .sidebar-container {
            background: #ffffff;
            border-right: 1px solid #f1f5f9;
        }

        .nav-item {
            position: relative;
            transition: all 0.2s ease-in-out;
            border-radius: 12px; /* More rounded */
            margin-bottom: 6px; /* More spacing */
            color: #64748b; /* Slate 500 */
            font-weight: 500;
        }

        .nav-item:hover {
            color: #1e293b; /* Slate 800 */
            background: #f8fafc; /* Slate 50 */
            transform: translateX(4px); /* Subtle movement */
        }

        .nav-item-active {
            color: #4f46e5 !important; /* Indigo 600 */
            background: #eef2ff; /* Indigo 50 */
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.1), 0 2px 4px -1px rgba(99, 102, 241, 0.06);
        }
        
        .nav-item-active::before {
            content: '';
            position: absolute;
            left: -12px; /* Outside the container effectively, or nicely aligned */
            top: 50%;
            transform: translateY(-50%);
            height: 20px;
            width: 4px;
            background: #4f46e5;
            border-radius: 0 4px 4px 0;
            display: none; /* Hidden for the pill look, optional to enable */
        }

        .nav-item-active i { color: #4f46e5 !important; }

        /* Floating Header */
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(241, 245, 249, 0.8);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02);
        }
        
        .mobile-overlay {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
        }
        
        .fade-enter { animation: fadeEnter 0.4s ease-out; }
        @keyframes fadeEnter {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="text-slate-600 antialiased h-screen overflow-hidden flex bg-slate-50">

    @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('demo-admin'))
    <div class="bg-amber-500 text-white text-xs font-bold text-center py-1 fixed top-0 left-0 right-0 z-[60] shadow-md">
        <i class="fas fa-exclamation-triangle mr-1"></i> DEMO MODE: View Only
    </div>
    @endif
    
    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 mobile-overlay z-40 hidden transition-opacity opacity-0 md:hidden"></div>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="w-[280px] sidebar-container flex-shrink-0 flex flex-col z-50 transition-transform duration-300 fixed md:static inset-y-0 left-0 transform -translate-x-full md:translate-x-0 h-full {{ (Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('demo-admin')) ? 'mt-6' : '' }} shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        
        <!-- Brand Section -->
        <div class="h-24 flex items-center px-6 border-b border-slate-100">
            <div class="flex items-center gap-4 w-full group cursor-pointer">
                <div class="relative w-11 h-11 flex items-center justify-center">
                    <div class="absolute inset-0 bg-indigo-500 rounded-2xl blur-lg opacity-20 group-hover:opacity-40 transition-opacity"></div>
                    <div class="relative w-full h-full bg-gradient-to-br from-indigo-600 to-violet-600 rounded-xl flex items-center justify-center text-white shadow-lg transform group-hover:rotate-6 transition-transform duration-300">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="font-extrabold text-xl text-slate-800 tracking-tight leading-none group-hover:text-indigo-600 transition-colors">{{ \App\Models\Setting::get('app_name', config('app.name')) }}</h1>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Admin Panel</span>
                </div>
            </div>
            <!-- Mobile Close -->
            <button id="mobile-menu-close" class="md:hidden ml-auto text-slate-400 hover:text-indigo-600 p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Navigation Scroll Area -->
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-8 custom-scrollbar">
            
            <!-- Section: Overview -->
            <div>
                <p class="px-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-3">Overview</p>
                <div class="space-y-1">
                    @if(Auth::guard('admin')->user()->hasPermission('dashboard_access'))
                    <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-house w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Dashboard</span>
                    </a>
                    @endif
                    
                    <a href="{{ route('admin.profile.edit') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.profile.edit') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-user-circle w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.profile.edit') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>My Profile</span>
                    </a>
                </div>
            </div>

            <!-- Section: Management -->
            <div>
                <p class="px-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-3">Management</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.users.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.users.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-users w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Users</span>
                    </a>

                    @if(Auth::guard('admin')->user()->hasPermission('manage_staff'))
                    <a href="{{ route('admin.staff.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.staff.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-shield-alt w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.staff.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Staff</span>
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.roles.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-user-lock w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.roles.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Roles & Permissions</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Section: Content -->
            <div>
                <p class="px-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-3">Content</p>
                <div class="space-y-1">
                    @if(Auth::guard('admin')->user()->hasPermission('manage_settings'))
                    <a href="{{ route('admin.categories.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.categories.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-layer-group w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.categories.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Categories</span>
                    </a>
                    <a href="{{ route('admin.wallpapers.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.wallpapers.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-image w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.wallpapers.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Wallpapers</span>
                    </a>
                    <a href="{{ route('admin.games.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.games.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-gamepad w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.games.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Games</span>
                    </a>
                    <a href="{{ route('admin.shorts.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.shorts.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-video w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.shorts.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Shorts</span>
                    </a>
                    @endif

                    <a href="{{ route('admin.pages.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.pages.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-file-lines w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.pages.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Pages</span>
                    </a>

                    @if(Auth::guard('admin')->user()->hasPermission('manage_banners'))
                    <a href="{{ route('admin.banners.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.banners.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-ad w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.banners.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Banners</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Section: Monetization -->
            @if(Auth::guard('admin')->user()->hasPermission('manage_settings'))
            <div>
                <p class="px-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-3">Monetization</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.daily_rewards.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.daily_rewards.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-gift w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.daily_rewards.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Daily Rewards</span>
                    </a>
                    <a href="{{ route('admin.redeem.requests.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.redeem.requests.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-money-bill-wave w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.redeem.requests.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Withdrawals</span>
                    </a>
                    <a href="{{ route('admin.redeem.gateways.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.redeem.gateways.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-wallet w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.redeem.gateways.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Payment Gateways</span>
                    </a>
                </div>
            </div>

            <!-- Section: System -->
            <div>
                <p class="px-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-3">System</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.support.index') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.support.*') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-headset w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.support.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Help Center</span>
                    </a>
                    <a href="{{ route('admin.settings.general') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.settings.general') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-cog w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.settings.general') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>General Settings</span>
                    </a>
                    <a href="{{ route('admin.settings.ads') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.settings.ads') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-bullhorn w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.settings.ads') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Ad Networks</span>
                    </a>
                    <a href="{{ route('admin.settings.api') }}" class="nav-item flex items-center px-4 py-3.5 text-sm font-medium group {{ request()->routeIs('admin.settings.api') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-code w-5 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('admin.settings.api') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>API Keys</span>
                    </a>
                </div>
            </div>
            @endif

        </div>

        <!-- User Profile (Bottom Sidebar) -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-3 p-2.5 rounded-xl transition-all hover:bg-white hover:shadow-md group cursor-pointer border border-transparent hover:border-slate-100">
                <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden border-2 border-white shadow-sm group-hover:border-indigo-100 transition-colors">
                     <img src="https://ui-avatars.com/api/?name={{ Auth::guard('admin')->user()->name ?? 'Admin' }}&background=6366f1&color=fff" alt="Admin" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate group-hover:text-indigo-600 transition-colors">{{ Auth::guard('admin')->user()->name ?? 'Administrator' }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <p class="text-[10px] text-slate-500 truncate font-medium">Online</p>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors rounded-lg hover:bg-rose-50" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[#f1f5f9] {{ (Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('demo-admin')) ? 'pt-6' : '' }}">
        
        <!-- Header -->
        <header class="glass-header h-16 flex items-center justify-between px-6 z-30 flex-shrink-0 sticky top-0">
            <!-- Left: Mobile Toggle & Title -->
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="md:hidden p-2 text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">@yield('header', 'Dashboard')</h2>
                    <p class="text-[10px] text-slate-500 hidden sm:block">Welcome back to admin panel</p>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center gap-4">
                <!-- Search -->
                <div class="hidden md:block relative group">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                    <input type="text" placeholder="Quick Search..." class="pl-10 pr-4 py-2 rounded-xl bg-white/50 border border-slate-200 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-500/10 text-sm w-64 transition-all placeholder-slate-400 text-slate-700 hover:bg-white">
                </div>

                <div class="h-8 w-px bg-slate-200 mx-2"></div>

                <!-- Notifications -->
                <button class="relative p-2.5 text-slate-500 hover:text-indigo-600 transition-colors rounded-xl hover:bg-indigo-50">
                    <i class="far fa-bell text-lg"></i>
                    <span class="absolute top-2.5 right-3 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
                </button>
                
                <!-- Settings Link -->
                <a href="{{ route('admin.settings.general') }}" class="p-2.5 text-slate-500 hover:text-indigo-600 transition-colors rounded-xl hover:bg-indigo-50" title="Settings">
                    <i class="fas fa-cog text-lg animate-spin-slow-hover"></i>
                </a>
            </div>
        </header>

        <!-- Content Scroll Area -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto p-6 scroll-smooth">
            <div class="max-w-7xl mx-auto fade-enter pb-10">
                
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-6 bg-white/50 inline-flex px-3 py-1.5 rounded-lg border border-slate-100 shadow-sm backdrop-blur-sm">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors flex items-center gap-1">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                    <span class="text-slate-600 font-semibold">@yield('header', 'Page')</span>
                </nav>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 flex items-center p-4 bg-emerald-50 rounded-xl border border-emerald-100 shadow-sm animate-pulse">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 shadow-sm">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-bold text-emerald-800">Success</h3>
                            <div class="text-sm text-emerald-600 mt-0.5">{{ session('success') }}</div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-700 transition-colors p-1 hover:bg-emerald-100 rounded">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 flex items-start p-4 bg-rose-50 rounded-xl border border-rose-100 shadow-sm">
                        <div class="flex-shrink-0 w-8 h-8 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 mt-0.5 shadow-sm">
                            <i class="fas fa-exclamation text-sm"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-bold text-rose-800">Error</h3>
                            <ul class="list-disc list-inside text-sm text-rose-600 mt-1 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="this.parentElement.remove()" class="ml-auto text-rose-400 hover:text-rose-700 transition-colors p-1 hover:bg-rose-100 rounded">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="text-center py-6 text-xs text-slate-400 font-medium border-t border-slate-200 mt-auto">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('app_name', 'Admin Panel') }}. Crafted with <i class="fas fa-heart text-rose-400 mx-0.5 animate-pulse"></i> by Admin
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Logic
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('mobile-menu-close');
            const sidebarEl = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

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

            if (mobileBtn) mobileBtn.addEventListener('click', toggleMenu);
            if (closeBtn) closeBtn.addEventListener('click', toggleMenu);
            if (overlay) overlay.addEventListener('click', toggleMenu);
        });
    </script>
    @stack('scripts')
</body>
</html>