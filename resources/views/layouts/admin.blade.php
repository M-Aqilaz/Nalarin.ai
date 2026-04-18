<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Nalarin.ai') }} - Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:500,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-outfit { font-family: 'Outfit', sans-serif; }
            .font-inter { font-family: 'Inter', sans-serif; }
            
            /* Filament-like styles */
            .fi-sidebar {
                background-color: #18181b; /* zinc-900 */
                border-right: 1px solid #27272a; /* zinc-800 */
            }
            .fi-sidebar-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.5rem 0.75rem;
                border-radius: 0.5rem;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all 0.2s;
            }
            .fi-sidebar-item-active {
                background-color: rgba(168, 85, 247, 0.1); /* purple-500/10 */
                color: #c084fc; /* purple-400 */
            }
            .fi-sidebar-item-inactive {
                color: #a1a1aa; /* zinc-400 */
            }
            .fi-sidebar-item-inactive:hover {
                background-color: rgba(255, 255, 255, 0.05);
                color: #e4e4e7; /* zinc-200 */
            }
            .fi-group-label {
                font-size: 0.75rem;
                font-weight: 600;
                color: #71717a; /* zinc-500 */
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 0.5rem;
                padding-left: 0.75rem;
            }
            
            .fi-main-bg {
                background-color: #09090b; /* zinc-950 */
            }
            
            /* Custom Scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
            ::-webkit-scrollbar-thumb:hover { background: #52525b; }
        </style>
    </head>
    <body class="font-inter antialiased fi-main-bg text-gray-100 flex h-screen overflow-hidden">
        
        <!-- Filament-like Sidebar -->
        <aside class="w-64 fi-sidebar flex flex-col h-full shrink-0 z-20">
            <!-- Logo Section -->
            <div class="h-16 flex items-center px-6 border-b border-zinc-800">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-purple-500/20">
                        N
                    </div>
                    <span class="font-inter font-bold text-lg tracking-tight text-white">Nalarin Admin</span>
                </a>
            </div>

            <!-- Navigation Section -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-6">
                
                <!-- Group: Dashboard -->
                <div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="fi-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'fi-sidebar-item-active' : 'fi-sidebar-item-inactive' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group: Management -->
                <div>
                    <div class="fi-group-label">Management</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="fi-sidebar-item {{ request()->routeIs('admin.users.*') ? 'fi-sidebar-item-active' : 'fi-sidebar-item-inactive' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Manajemen User</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.documents.index') }}" class="fi-sidebar-item {{ request()->routeIs('admin.documents.*') ? 'fi-sidebar-item-active' : 'fi-sidebar-item-inactive' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Manajemen Materi</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- User Profile Section -->
            <div class="p-4 border-t border-zinc-800">
                <div class="flex items-center gap-3 w-full">
                    <div class="w-9 h-9 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0">
                        <span class="text-zinc-300 font-medium text-sm">A</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">Administrator</p>
                        <p class="text-xs text-zinc-500 truncate">admin@nalarin.ai</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-zinc-950">
            
            <!-- Mobile Header -->
            <header class="md:hidden h-16 border-b border-zinc-800 bg-zinc-900 flex items-center justify-between px-4 z-20">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">N</div>
                </div>
                <button class="text-zinc-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <!-- Page Header -->
            @isset($header)
                <header class="py-6 px-6 md:px-8 border-b border-zinc-800/50 bg-zinc-900/30 flex-shrink-0">
                    <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {{ $header }}
                        @isset($headerActions)
                            <div>{{ $headerActions }}</div>
                        @endisset
                    </div>
                </header>
            @endisset

            <!-- Page Content scrollable -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto h-full">
                    {{ $slot }}
                </div>
            </main>
        </div>

    </body>
</html>
