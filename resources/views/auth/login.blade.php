<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Nalarin.ai</title>
    <link rel="icon" href="{{ asset('images/logo_nalarin_ai.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        .glass-card {
            background: rgba(31, 41, 55, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .blob {
            position: absolute;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.4;
            border-radius: 50%;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .social-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.95rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(17, 24, 39, 0.75);
            color: #f9fafb;
            font-weight: 600;
            transition: all 200ms ease;
        }
        .social-button:hover {
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
            background: rgba(31, 41, 55, 0.9);
        }
        .social-button--disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="font-inter antialiased bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="blob bg-purple-600 w-96 h-96 top-[-10%] left-[-10%]"></div>
    <div class="blob bg-blue-600 w-[30rem] h-[30rem] bottom-[-20%] right-[-10%]"></div>

    <div class="z-10 w-full max-w-md px-6 py-12 animate-float">
        <div class="mb-8 text-center">
            <a href="/" class="inline-flex items-center gap-2 mb-4 transition-transform duration-300 hover:scale-105">
                <img src="{{ asset('images/logo_nalarin_ai.png') }}" class="h-12 w-12 object-contain" alt="Nalarin.ai Logo">
                <span class="font-outfit text-3xl font-bold tracking-tight text-white">Nalarin<span class="text-purple-400">.ai</span></span>
            </a>
            <h2 class="mt-2 font-outfit text-2xl font-bold text-white">Selamat Datang Kembali</h2>
            <p class="mt-2 text-sm text-gray-400">Masuk untuk melanjutkan pengalaman belajarmu dengan AI.</p>
        </div>

        <div class="glass-card relative overflow-hidden rounded-3xl p-8 shadow-2xl">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/5 to-transparent"></div>

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
                <div class="relative z-10 mb-4 rounded-xl border border-green-500/20 bg-green-500/10 p-3 text-sm font-medium text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('social'))
                <div class="relative z-10 mb-4 rounded-xl border border-red-500/20 bg-red-500/10 p-3 text-sm font-medium text-red-300">
                    {{ $errors->first('social') }}
                </div>
            @endif

            <div class="relative z-10 space-y-3">
                @if ($googleReady)
                    <a href="{{ route('auth.google.redirect') }}" class="social-button">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.9-5.5 3.9-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.2.8 3.9 1.5l2.7-2.6C16.9 3.4 14.7 2.5 12 2.5A9.5 9.5 0 0 0 2.5 12 9.5 9.5 0 0 0 12 21.5c5.5 0 9.1-3.9 9.1-9.3 0-.6-.1-1.1-.2-1.6H12Z"/>
                        </svg>
                        <span>Lanjutkan dengan Google</span>
                    </a>
                @else
                    <button type="button" class="social-button social-button--disabled" disabled>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.9-5.5 3.9-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.2.8 3.9 1.5l2.7-2.6C16.9 3.4 14.7 2.5 12 2.5A9.5 9.5 0 0 0 2.5 12 9.5 9.5 0 0 0 12 21.5c5.5 0 9.1-3.9 9.1-9.3 0-.6-.1-1.1-.2-1.6H12Z"/>
                        </svg>
                        <span>Google segera tersedia</span>
                    </button>
                @endif

                @if ($discordReady)
                    <a href="{{ route('auth.discord.redirect') }}" class="social-button">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#5865F2" d="M20.3 4.8A16.5 16.5 0 0 0 16.2 3c-.2.4-.5 1-.7 1.4a15.3 15.3 0 0 0-7 0A9.4 9.4 0 0 0 7.8 3C6.3 3.5 5 4 3.7 4.8 1.1 8.6.4 12.3.8 16c1.7 1.3 3.4 2.1 5.1 2.6.4-.6.8-1.2 1.1-1.8l-1.7-.8c.2-.1.3-.2.5-.3 3.3 1.5 6.9 1.5 10.1 0 .2.1.3.2.5.3l-1.7.8c.3.6.7 1.2 1.1 1.8 1.7-.5 3.4-1.3 5.1-2.6.5-4.3-.8-8-3.7-11.2ZM8.7 13.7c-1 0-1.8-.9-1.8-2s.8-2 1.8-2 1.8.9 1.8 2-.8 2-1.8 2Zm6.6 0c-1 0-1.8-.9-1.8-2s.8-2 1.8-2 1.8.9 1.8 2-.8 2-1.8 2Z"/>
                        </svg>
                        <span>Lanjutkan dengan Discord</span>
                    </a>
                @else
                    <button type="button" class="social-button social-button--disabled" disabled>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#5865F2" d="M20.3 4.8A16.5 16.5 0 0 0 16.2 3c-.2.4-.5 1-.7 1.4a15.3 15.3 0 0 0-7 0A9.4 9.4 0 0 0 7.8 3C6.3 3.5 5 4 3.7 4.8 1.1 8.6.4 12.3.8 16c1.7 1.3 3.4 2.1 5.1 2.6.4-.6.8-1.2 1.1-1.8l-1.7-.8c.2-.1.3-.2.5-.3 3.3 1.5 6.9 1.5 10.1 0 .2.1.3.2.5.3l-1.7.8c.3.6.7 1.2 1.1 1.8 1.7-.5 3.4-1.3 5.1-2.6.5-4.3-.8-8-3.7-11.2ZM8.7 13.7c-1 0-1.8-.9-1.8-2s.8-2 1.8-2 1.8.9 1.8 2-.8 2-1.8 2Zm6.6 0c-1 0-1.8-.9-1.8-2s.8-2 1.8-2 1.8.9 1.8 2-.8 2-1.8 2Z"/>
                        </svg>
                        <span>Discord segera tersedia</span>
                    </button>
                @endif
            </div>

            <div class="relative z-10 my-6 flex items-center gap-4">
                <div class="h-px flex-1 bg-white/10"></div>
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">atau masuk dengan email</span>
                <div class="h-px flex-1 bg-white/10"></div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="relative z-10 space-y-6">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-300">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-xl border border-white/10 bg-gray-900/50 px-5 py-3.5 text-white placeholder-gray-500 shadow-inner transition-all focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-purple-400 transition-colors hover:text-purple-300">Lupa Password?</a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-xl border border-white/10 bg-gray-900/50 px-5 py-3.5 text-white placeholder-gray-500 shadow-inner transition-all focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="********">
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 cursor-pointer rounded border-gray-600 bg-gray-800 text-purple-500 focus:ring-purple-500 focus:ring-offset-gray-950">
                    <label for="remember_me" class="ml-2 block cursor-pointer text-sm text-gray-400">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 px-4 py-4 text-lg font-bold text-white shadow-[0_0_20px_rgba(168,85,247,0.3)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(168,85,247,0.5)]">
                    Masuk Sekarang
                </button>
            </form>

            <div class="relative z-10 mt-8 border-t border-white/10 pt-6 text-center text-sm text-gray-400">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-purple-400 transition-colors hover:text-purple-300">Daftar secara gratis</a>
            </div>
        </div>
    </div>
</body>
</html>
