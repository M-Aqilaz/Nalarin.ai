<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
                    <a href="{{ route('dashboard') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">{{ __('ui.dashboard') }}</a>
                    <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('ui.ai_learning') }}</p></div>
                    <a href="{{ route('feature.upload') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.upload') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">{{ __('ui.upload_material') }}</a>
                    <a href="{{ route('feature.summary') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.summary') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">{{ __('ui.summary') }}</a>
                    <a href="{{ route('feature.chat') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.chat') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">{{ __('ui.ai_tutor') }}</a>
                    <a href="{{ route('feature.flashcards') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.flashcards') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">{{ __('ui.flashcards') }}</a>
                    <a href="{{ route('feature.quiz') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.quiz') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">{{ __('ui.quiz') }}</a>
                    <a href="{{ route('feature.pomodoro') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('feature.pomodoro') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">Pomodoro</a>
                    <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('ui.social_learning') }}</p></div>
                    <a href="{{ route('rooms.index') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('rooms.*') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">{{ __('ui.class_room') }}</a>
                    <a href="{{ route('matchmaking.index') }}" class="block px-3 py-3 rounded-xl {{ request()->routeIs('matchmaking.*', 'matches.*') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">{{ __('ui.study_matching') }}</a>
                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-3 rounded-xl {{ request()->routeIs('notifications.*') ? 'border border-sky-300 bg-white text-sky-800 shadow-sm' : 'text-slate-700 hover:bg-white/70 hover:text-sky-800' }}">
                        <span>{{ __('ui.notifications') }}</span>
                        @if ($unreadNotificationCount > 0)
                            <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-semibold text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                        @endif
                    </a>
                </nav>

                <div class="border-t border-sky-200 p-4 space-y-3">
                    <div class="rounded-2xl border border-sky-200 bg-white/60 p-3">
                        <p class="text-sm font-bold text-slate-950 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-700 truncate">{{ Auth::user()->email }}</p>
                        <p class="text-[10px] text-slate-900 mt-2 uppercase">{{ __('ui.plan') }} {{ Auth::user()->plan }} | {{ __('ui.match') }} {{ Auth::user()->match_credits }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('profile.edit') }}" class="rounded-xl border border-sky-200 bg-white/60 px-3 py-2.5 text-center text-xs text-slate-800">{{ __('ui.profile') }}</a>
                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-sky-200 bg-white/60 px-3 py-2.5 text-center text-xs text-slate-800 {{ Auth::user()->role === 'admin' ? '' : 'hidden' }}">Admin</a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-red-300 bg-red-50 px-3 py-2.5 text-left text-sm text-red-600">{{ __('ui.logout') }}</button>
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
                    <x-language-switch compact />
                    <a href="{{ route('notifications.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-sky-200 bg-white/70 text-slate-800 transition hover:bg-white {{ request()->routeIs('notifications.*') ? 'ring-1 ring-cyan-300/60' : '' }}" aria-label="Notifikasi">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"></path></svg>
                        @if ($unreadNotificationCount > 0)
                            <span class="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-800">{{ __('ui.home') }}</a>
                </div>
            </header>

            @isset($header)
                <header class="{{ $isPomodoroPage ? 'px-4 py-3 md:px-5' : 'px-4 py-5 md:px-8 md:py-6' }} flex-shrink-0 border-b border-sky-200/70 bg-white/10 backdrop-blur">
                    <div class="mx-auto flex max-w-7xl flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="min-w-0">{{ $header }}</div>
                        <div class="flex w-full flex-col gap-3 md:w-auto md:shrink-0 md:flex-row md:items-center md:justify-end">
                            <x-language-switch class="hidden md:inline-flex" />
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

            <main class="relative flex-1 {{ $isPomodoroPage ? 'overflow-y-auto p-2 md:overflow-hidden md:p-3' : 'overflow-y-auto bg-gradient-to-b from-transparent via-transparent to-white/80 px-4 pb-12 pt-4 md:px-8 md:pb-16 md:pt-6' }}">
                <div class="mx-auto min-h-full max-w-7xl {{ $isPomodoroPage ? '' : 'pb-8 md:pb-12' }}">{{ $slot }}</div>
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
