<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Nalarin.ai</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="font-inter min-h-screen bg-sky-50 text-slate-950 antialiased selection:bg-sky-200">
    <x-page-loader />

    <main class="relative isolate flex min-h-screen items-center justify-center overflow-hidden px-5 py-10">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_18%_15%,rgba(255,246,178,0.62),transparent_12%),radial-gradient(circle_at_82%_12%,rgba(255,255,255,0.95),transparent_14%),linear-gradient(135deg,#e9f8ff_0%,#ccefff_42%,#f8fdff_74%,#ddf7ff_100%)]"></div>
        <div class="absolute -left-24 bottom-10 -z-10 h-72 w-72 rounded-full bg-sky-300/30 blur-3xl"></div>
        <div class="absolute -right-24 top-10 -z-10 h-80 w-80 rounded-full bg-cyan-200/50 blur-3xl"></div>

        <section class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-sky-200 bg-white/88 shadow-[0_24px_70px_rgba(14,116,144,0.18)] backdrop-blur-xl lg:grid-cols-[0.95fr_1.05fr]">
            <aside class="relative hidden min-h-[620px] overflow-hidden bg-gradient-to-br from-sky-100 via-white to-cyan-100 p-8 lg:block">
                <a href="{{ url('/') }}" class="inline-flex items-center">
                    <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-10 w-auto max-w-[200px] object-contain" alt="Nalarin.ai Logo">
                </a>

                <div class="mx-auto mt-8 flex h-[270px] w-[330px] items-center justify-center">
                    <img src="{{ asset('images/NALA.png') }}" class="max-h-full w-auto object-contain drop-shadow-[0_24px_40px_rgba(14,116,144,0.22)]" alt="Nala">
                </div>

                <div class="mt-8 max-w-sm">
                    <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-sky-700">Learning Hub</p>
                    <h1 class="mt-4 font-outfit text-4xl font-extrabold leading-tight text-slate-950">Masuk lagi, lanjutkan ritme belajarmu.</h1>
                    <p class="mt-5 text-base leading-7 text-slate-700">Nala siap bantu rangkum materi, bikin flashcard, dan menemani latihan soal dalam satu ruang belajar.</p>
                </div>
            </aside>

            <section class="p-6 sm:p-8 lg:p-10">
                <div class="mb-8 flex items-center justify-between gap-4 lg:hidden">
                    <a href="{{ url('/') }}" class="inline-flex items-center">
                        <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-10 w-auto max-w-[190px] object-contain" alt="Nalarin.ai Logo">
                    </a>
                    <a href="{{ url('/') }}" class="rounded-xl border border-sky-200 bg-white px-4 py-2 text-sm font-bold text-sky-800 shadow-sm">Beranda</a>
                </div>

                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-sky-700">Login</p>
                    <h2 class="mt-3 font-outfit text-3xl font-extrabold text-slate-950">Selamat datang kembali</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Masuk untuk melanjutkan pengalaman belajarmu dengan AI.</p>
                </div>

                <div class="mt-8">
                    @php
                        $googleReady = Route::has('auth.google.redirect')
                            && filled(config('services.google.client_id'))
                            && filled(config('services.google.client_secret'))
                            && filled(config('services.google.redirect'));

                        $discordReady = Route::has('auth.discord.redirect')
                            && filled(config('services.discord.client_id'))
                            && filled(config('services.discord.client_secret'))
                            && filled(config('services.discord.redirect'));
                    @endphp

                    @if (session('status'))
                        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->has('social'))
                        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-3 text-sm font-semibold text-rose-700">
                            {{ $errors->first('social') }}
                        </div>
                    @endif

                    <div class="grid gap-3 sm:grid-cols-2">
                        @if ($googleReady)
                            <a href="{{ route('auth.google.redirect') }}" class="inline-flex items-center justify-center gap-3 rounded-2xl border border-sky-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm transition hover:border-sky-300 hover:bg-sky-50">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.9-5.5 3.9-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.2.8 3.9 1.5l2.7-2.6C16.9 3.4 14.7 2.5 12 2.5A9.5 9.5 0 0 0 2.5 12 9.5 9.5 0 0 0 12 21.5c5.5 0 9.1-3.9 9.1-9.3 0-.6-.1-1.1-.2-1.6H12Z"/>
                                </svg>
                                Google
                            </a>
                        @else
                            <button type="button" class="inline-flex cursor-not-allowed items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-400" disabled>
                                Google
                            </button>
                        @endif

                        @if ($discordReady)
                            <a href="{{ route('auth.discord.redirect') }}" class="inline-flex items-center justify-center gap-3 rounded-2xl border border-sky-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm transition hover:border-sky-300 hover:bg-sky-50">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="#5865F2" d="M20.3 4.8A16.5 16.5 0 0 0 16.2 3c-.2.4-.5 1-.7 1.4a15.3 15.3 0 0 0-7 0A9.4 9.4 0 0 0 7.8 3C6.3 3.5 5 4 3.7 4.8 1.1 8.6.4 12.3.8 16c1.7 1.3 3.4 2.1 5.1 2.6.4-.6.8-1.2 1.1-1.8l-1.7-.8c.2-.1.3-.2.5-.3 3.3 1.5 6.9 1.5 10.1 0 .2.1.3.2.5.3l-1.7.8c.3.6.7 1.2 1.1 1.8 1.7-.5 3.4-1.3 5.1-2.6.5-4.3-.8-8-3.7-11.2ZM8.7 13.7c-1 0-1.8-.9-1.8-2s.8-2 1.8-2 1.8.9 1.8 2-.8 2-1.8 2Zm6.6 0c-1 0-1.8-.9-1.8-2s.8-2 1.8-2 1.8.9 1.8 2-.8 2-1.8 2Z"/>
                                </svg>
                                Discord
                            </a>
                        @else
                            <button type="button" class="inline-flex cursor-not-allowed items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-400" disabled>
                                Discord
                            </button>
                        @endif
                    </div>

                    <div class="my-7 flex items-center gap-4">
                        <div class="h-px flex-1 bg-sky-100"></div>
                        <span class="text-xs font-extrabold uppercase tracking-[0.2em] text-slate-400">atau email</span>
                        <div class="h-px flex-1 bg-sky-100"></div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-2xl border border-sky-200 bg-white px-5 py-3.5 text-slate-950 placeholder-slate-400 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="nama@email.com">
                            @error('email')
                                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-sky-700 transition hover:text-sky-500">Lupa password?</a>
                                @endif
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-2xl border border-sky-200 bg-white px-5 py-3.5 text-slate-950 placeholder-slate-400 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Masukkan password">
                            @error('password')
                                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-sky-300 text-sky-500 focus:ring-sky-400">
                            <label for="remember_me" class="ml-2 block cursor-pointer text-sm font-semibold text-slate-600">
                                Ingat saya
                            </label>
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-sky-500 px-5 py-4 text-base font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:-translate-y-0.5 hover:bg-sky-600">
                            Masuk Sekarang
                        </button>
                    </form>

                    <div class="mt-7 rounded-2xl border border-sky-100 bg-sky-50 px-4 py-4 text-center text-sm font-semibold text-slate-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-extrabold text-sky-700 transition hover:text-sky-500">Daftar gratis</a>
                    </div>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
