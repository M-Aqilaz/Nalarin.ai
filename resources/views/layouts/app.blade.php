<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    @php($isPomodoroPage = request()->routeIs('feature.pomodoro'))
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Nalarin.ai') }}</title>
        <link rel="icon" href="{{ asset('images/logo_nalarin_ai.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .font-outfit { font-family: 'Outfit', sans-serif; }
            .font-inter { font-family: 'Inter', sans-serif; }
            .glass-panel {
                background: rgba(31, 41, 55, 0.7);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            [x-cloak] { display: none !important; }
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 3px; }
        </style>
    </head>
    <body x-data="{ mobileNavOpen: false }" class="font-inter antialiased bg-gray-950 text-gray-100 flex h-screen overflow-hidden selection:bg-purple-500/30">
        @include('layouts.sidebar')

        <div x-cloak x-show="mobileNavOpen" class="fixed inset-0 z-40 bg-gray-950/70 backdrop-blur-sm md:hidden" @click="mobileNavOpen = false"></div>

        <aside x-cloak x-show="mobileNavOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-full opacity-0" class="fixed inset-y-0 left-0 z-50 w-[86vw] max-w-sm glass-panel border-r border-white/10 md:hidden">
            <div class="flex h-full flex-col">
                <div class="h-16 flex items-center justify-between px-4 border-b border-white/5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo_nalarin_ai.png') }}" class="w-8 h-8 object-contain" alt="Nalarin.ai Logo">
                        <span class="font-outfit font-bold text-lg text-white">Nalarin<span class="text-purple-400">.ai</span></span>
                    </a>
                    <button type="button" class="rounded-xl border border-white/10 bg-white/5 p-2 text-gray-300" @click="mobileNavOpen = false">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Dashboard</a>
                    <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">AI Learning</p></div>
                    <a href="{{ route('feature.upload') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.upload') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Unggah Materi</a>
                    <a href="{{ route('feature.summary') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.summary') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Ringkasan</a>
                    <a href="{{ route('feature.chat') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.chat') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">AI Tutor</a>
                    <a href="{{ route('feature.flashcards') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.flashcards') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Flashcards</a>
                    <a href="{{ route('feature.quiz') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.quiz') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Kuis</a>
                    <a href="{{ route('feature.pomodoro') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.pomodoro') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Pomodoro</a>
                    <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Social Learning</p></div>
                    <a href="{{ route('rooms.index') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('rooms.*') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Group Chat Kelas</a>
                    <a href="{{ route('matchmaking.index') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('matchmaking.*', 'matches.*') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Study Matching</a>
                    <a href="{{ route('pricing') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('pricing') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">Pricing</a>
                </nav>

                <div class="border-t border-white/5 p-4 space-y-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        <p class="text-[10px] text-purple-300 mt-2 uppercase">Plan {{ Auth::user()->plan }} | Match {{ Auth::user()->match_credits }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('profile.edit') }}" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-center text-xs text-gray-200">Profil</a>
                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-center text-xs text-gray-200 {{ Auth::user()->role === 'admin' ? '' : 'hidden' }}">Admin</a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2.5 text-left text-sm text-red-300">Keluar</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 bg-gray-950/50 relative">
            <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-purple-900/10 to-transparent pointer-events-none -z-10"></div>

            <header class="md:hidden h-16 border-b border-white/5 glass-panel flex items-center justify-between px-4 z-20">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-xl border border-white/10 bg-white/5 p-2 text-gray-300" @click="mobileNavOpen = true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <img src="{{ asset('images/logo_nalarin_ai.png') }}" class="w-8 h-8 object-contain" alt="Nalarin.ai Logo">
                </div>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-300">Home</a>
            </header>

            @isset($header)
                <header class="{{ $isPomodoroPage ? 'py-3 px-4 md:px-5' : 'py-5 px-4 md:px-8 md:py-6' }} border-b border-white/5 glass-panel/50 flex-shrink-0">
                    <div class="max-w-6xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {{ $header }}
                        @isset($headerActions)
                            <div class="w-full md:w-auto">{{ $headerActions }}</div>
                        @endisset
                    </div>
                </header>
            @endisset

            <main class="flex-1 {{ $isPomodoroPage ? 'overflow-y-auto md:overflow-hidden p-2 md:p-3' : 'overflow-y-auto p-4 md:p-8 pb-8' }}">
                <div class="max-w-6xl mx-auto h-full">{{ $slot }}</div>
            </main>
        </div>

        @auth
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const trackElements = document.querySelectorAll('.track-feature, [data-feature]');
                    trackElements.forEach(el => {
                        el.addEventListener('click', function() {
                            const featureName = this.getAttribute('data-feature');
                            if (!featureName) return;
                            fetch("{{ route('feature.track') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ feature_name: featureName })
                            }).catch(() => {});
                        });
                    });
                });
            </script>
        @endauth
    </body>
</html>
