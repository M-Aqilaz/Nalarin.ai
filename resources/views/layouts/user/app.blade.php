<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    @php($isPomodoroPage = request()->routeIs('feature.pomodoro'))
    @php($unreadNotificationCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0)
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Nalarin.ai') }}</title>
        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|poppins:500,600,700,800|roboto:400,500,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .font-outfit,
            .font-poppins { font-family: 'Poppins', sans-serif; }
            .font-inter { font-family: 'Inter', sans-serif; }
            .font-roboto { font-family: 'Roboto', sans-serif; }
            .glass-panel {
                background: rgba(15, 23, 42, 0.55);
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            [x-cloak] { display: none !important; }
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.45); border-radius: 9999px; }
            .typing-dots {
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
            }
            .typing-dots span {
                width: 0.35rem;
                height: 0.35rem;
                border-radius: 9999px;
                background: rgba(156, 163, 175, 0.95);
                animation: typing-fade 1.2s infinite ease-in-out;
            }
            .typing-dots span:nth-child(2) {
                animation-delay: 0.2s;
            }
            .typing-dots span:nth-child(3) {
                animation-delay: 0.4s;
            }
            @keyframes typing-fade {
                0%, 80%, 100% {
                    opacity: 0.25;
                    transform: translateY(0);
                }
                40% {
                    opacity: 1;
                    transform: translateY(-1px);
                }
            }
        </style>
    </head>
    <body x-data="{ mobileNavOpen: false, sidebarCollapsed: localStorage.getItem('nalarin:sidebarCollapsed') === 'true', toggleSidebar() { this.sidebarCollapsed = !this.sidebarCollapsed; localStorage.setItem('nalarin:sidebarCollapsed', this.sidebarCollapsed ? 'true' : 'false'); } }" class="user-theme font-inter antialiased text-slate-950 flex min-h-screen overflow-x-hidden md:h-screen md:overflow-hidden selection:bg-sky-200/70">
        <x-page-loader />
        @include('layouts.user.sidebar', ['unreadNotificationCount' => $unreadNotificationCount])

        <div class="pointer-events-none fixed left-[18%] top-20 h-24 w-24 rotate-45 rounded-[1.5rem] border border-white/70 opacity-50"></div>
        <div class="pointer-events-none fixed right-10 top-3 h-24 w-24 border-2 border-dashed border-white/70 opacity-70"></div>
        <div class="pointer-events-none fixed right-0 bottom-0 h-72 w-72 rounded-full bg-white/70 blur-2xl"></div>

        <div x-cloak x-show="mobileNavOpen" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm md:hidden" @click="mobileNavOpen = false"></div>

        <aside x-cloak x-show="mobileNavOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-full opacity-0" class="fixed inset-y-0 left-0 z-50 w-[86vw] max-w-sm border-r border-sky-200 bg-sky-100/95 text-slate-950 shadow-2xl md:hidden">
            <div class="flex h-full flex-col">
                <div class="h-16 flex items-center justify-between px-4 border-b border-sky-200">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-9 w-auto max-w-[180px] object-contain" alt="Nalarin.ai Logo">
                    </a>
                    <button type="button" class="rounded-xl border border-sky-200 bg-white/60 p-2 text-slate-700 hover:bg-white" @click="mobileNavOpen = false">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('dashboard') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-9.5z"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">AI Learning</p></div>
                    <a href="{{ route('feature.upload') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('feature.upload') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01.88-7.903A5.5 5.5 0 0118.5 9.5h.5a3.5 3.5 0 010 7H7zm5-8v9m0 0l-3-3m3 3l3-3"></path></svg>
                        <span>Unggah Materi</span>
                    </a>
                    <a href="{{ route('feature.summary') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('feature.summary') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 5.5h10M7 9.5h10M7 13.5h6M6 3h12a1 1 0 011 1v16l-3.5-2-3.5 2-3.5-2L5 20V4a1 1 0 011-1z"></path></svg>
                        <span>Ringkasan</span>
                    </a>
                    <a href="{{ route('feature.chat') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('feature.chat') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5m-7 6l2.8-2H18a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h.5L6 20z"></path></svg>
                        <span>AI Tutor</span>
                    </a>
                    <a href="{{ route('feature.flashcards') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('feature.flashcards') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h10a2 2 0 012 2v8H10a2 2 0 00-2 2V7zm0 0V5a2 2 0 00-2-2H4v14a2 2 0 012-2h2"></path></svg>
                        <span>Flashcards</span>
                    </a>
                    <a href="{{ route('feature.quiz') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('feature.quiz') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.09 9a3 3 0 115.82 1c0 2-3 3-3 3m.09 4h.01M4 6.5l8-3 8 3v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9v-5z"></path></svg>
                        <span>Kuis</span>
                    </a>
                    <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Focus Section</p></div>
                    <a href="{{ route('feature.pomodoro') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('feature.pomodoro') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l2.5 2.5M12 21a8 8 0 100-16 8 8 0 000 16zm-3-18h6"></path></svg>
                        <span>Pomodoro</span>
                    </a>
                    <a href="{{ route('feature.focus-planner') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('feature.focus-planner') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8M8 12h8M8 17h5M6 3h12a1 1 0 011 1v16l-3-2-4 2-4-2-3 2V4a1 1 0 011-1z"></path></svg>
                        <span>Focus Planner</span>
                    </a>
                    <a href="{{ route('feature.focus-insights') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('feature.focus-insights') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19h16M7 16V9m5 7V5m5 11v-4"></path></svg>
                        <span>Focus Insights</span>
                    </a>
                    <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Social Learning</p></div>
                    <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('rooms.*') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5V10l-5-7-5 7v10h5m-5 0H7a2 2 0 01-2-2v-5m7 7v-4a2 2 0 00-2-2H8a2 2 0 00-2 2v4m0 0H2"></path></svg>
                        <span>Group Chat Kelas</span>
                    </a>
                    <a href="{{ route('matchmaking.roulette') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 {{ request()->routeIs('matchmaking.*', 'matches.*') ? 'bg-white/70 text-slate-950 shadow-sm' : 'text-slate-800 hover:bg-white/60' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s-6.5-4.35-8.5-8A4.9 4.9 0 014 6.7 4.8 4.8 0 017.6 5a4.7 4.7 0 014.4 2.7A4.7 4.7 0 0116.4 5 4.8 4.8 0 0120 6.7a4.9 4.9 0 01.5 6.3C18.5 16.65 12 21 12 21z"></path></svg>
                        <span>Study Matching</span>
                    </a>
                </nav>

                <div class="border-t border-sky-200 p-4 space-y-3">
                    <div class="rounded-2xl border border-sky-200 bg-white/60 p-3">
                        <p class="text-sm font-bold text-slate-950 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-700 truncate">{{ Auth::user()->email }}</p>
                        <p class="text-[10px] text-slate-900 mt-2 uppercase">Plan {{ Auth::user()->plan }} | Match {{ Auth::user()->match_credits }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('profile.edit') }}" class="rounded-xl border border-sky-200 bg-white/60 px-3 py-2.5 text-center text-xs text-slate-800">Profil</a>
                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-sky-200 bg-white/60 px-3 py-2.5 text-center text-xs text-slate-800 {{ Auth::user()->role === 'admin' ? '' : 'hidden' }}">Admin</a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-red-300 bg-red-50 px-3 py-2.5 text-left text-sm text-red-600">Keluar</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="relative flex-1 min-w-0 flex-col md:flex">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-44 bg-white/20"></div>

            <header class="z-20 flex h-16 items-center justify-between border-b border-sky-200 bg-sky-100/80 px-4 backdrop-blur md:hidden">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-xl border border-sky-200 bg-white/60 p-2 text-slate-700 hover:bg-white" @click="mobileNavOpen = true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-8 w-auto max-w-[150px] object-contain" alt="Nalarin.ai Logo">
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('notifications.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-sky-200 bg-white/70 text-slate-800 transition hover:bg-white {{ request()->routeIs('notifications.*') ? 'ring-1 ring-cyan-300/60' : '' }}" aria-label="Notifikasi">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"></path></svg>
                        @if ($unreadNotificationCount > 0)
                            <span class="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-800">Home</a>
                </div>
            </header>

            @isset($header)
                <header class="{{ $isPomodoroPage ? 'px-4 py-3 md:px-5' : 'px-4 py-5 md:px-8 md:py-6' }} flex-shrink-0 border-b border-sky-200/70 bg-white/10 backdrop-blur">
                    <div class="mx-auto flex max-w-7xl flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="min-w-0">{{ $header }}</div>
                        <div class="flex w-full flex-col gap-3 md:w-auto md:shrink-0 md:flex-row md:items-center md:justify-end">
                            <a href="{{ route('notifications.index') }}" class="relative hidden md:inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-sky-200 bg-white/65 text-slate-800 shadow-sm transition hover:bg-white {{ request()->routeIs('notifications.*') ? 'ring-1 ring-cyan-300/60 bg-white' : '' }}" aria-label="Notifikasi">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"></path></svg>
                                @if ($unreadNotificationCount > 0)
                                    <span class="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                                @endif
                            </a>
                            @isset($headerActions)
                                <div class="w-full md:w-auto">{{ $headerActions }}</div>
                            @endisset
                        </div>
                    </div>
                </header>
            @endisset

            <main class="relative flex-1 {{ $isPomodoroPage ? 'overflow-y-auto p-2 md:overflow-hidden md:p-3' : 'overflow-y-auto px-4 pb-8 pt-4 md:px-8 md:pb-10 md:pt-6' }}">
                <div class="mx-auto h-full max-w-7xl">{{ $slot }}</div>
            </main>
        </div>

        @auth
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const trackElements = document.querySelectorAll('.track-feature, [data-feature]');
                    const trackUrl = "{{ route('feature.track') }}";

                    const sendFeatureTracking = (featureName) => {
                        if (!featureName) {
                            return;
                        }

                        const payload = JSON.stringify({ feature_name: featureName });

                        if (navigator.sendBeacon) {
                            const sent = navigator.sendBeacon(trackUrl, new Blob([payload], {
                                type: 'application/json'
                            }));

                            if (sent) {
                                return;
                            }
                        }

                        fetch(trackUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: payload,
                            keepalive: true,
                            credentials: 'same-origin'
                        }).catch(() => {});
                    };

                    trackElements.forEach(el => {
                        el.addEventListener('click', function() {
                            sendFeatureTracking(this.getAttribute('data-feature'));
                        });
                    });
                });
            </script>
        @endauth
    </body>
</html>
