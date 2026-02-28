<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EasyColoc') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="bg-slate-50">
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden"></div>

    <aside id="sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen transition-transform -translate-x-full md:translate-x-0 bg-white border-r border-slate-200">
        <div class="flex flex-col h-full">
            <div class="px-6 py-8 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <span class="text-xl font-bold text-slate-900">EasyColoc</span>
            </div>

            <div class="p-6">
                <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">Score: <span class="text-emerald-600 font-bold">+{{ auth()->user()->reputation ?? 0 }}</span></p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-2 overflow-y-auto">
                <a href="{{ route('colocations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('colocations.*') ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-50' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Colocations
                </a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.*') ? 'bg-purple-600 text-white' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Admin
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-50' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profil
                </a>
            </nav>

            <div class="p-4 border-t border-slate-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-rose-600 hover:bg-rose-50 font-bold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="md:ml-64 min-h-screen">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-8 sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <button id="sidebarToggle" class="md:hidden text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
                <h2 class="text-xl font-bold text-slate-900">@yield('page-title')</h2>
            </div>
            @yield('header-actions')
        </header>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>
</body>
</html>
