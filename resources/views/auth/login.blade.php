<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Nalarin.ai</title>
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
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="font-inter antialiased bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Background Blobs -->
    <div class="blob bg-purple-600 w-96 h-96 top-[-10%] left-[-10%]"></div>
    <div class="blob bg-blue-600 w-[30rem] h-[30rem] bottom-[-20%] right-[-10%]"></div>

    <div class="w-full max-w-md px-6 py-12 z-10 animate-float">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 mb-4 hover:scale-105 transition-transform duration-300">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-purple-500/30">
                    P
                </div>
                <span class="font-outfit font-bold text-3xl tracking-tight text-white">Nalarin<span class="text-purple-400">.ai</span></span>
            </a>
            <h2 class="font-outfit text-2xl font-bold text-white mt-2">Selamat Datang Kembali</h2>
            <p class="text-gray-400 mt-2 text-sm">Masuk untuk melanjutkan pengalaman belajarmu dengan AI.</p>
        </div>

        <div class="glass-card rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>
            
            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-400 p-3 bg-green-500/10 border border-green-500/20 rounded-xl relative z-10">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6 relative z-10">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full px-5 py-3.5 rounded-xl bg-gray-900/50 border border-white/10 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all shadow-inner" placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-purple-400 hover:text-purple-300 transition-colors">Lupa Password?</a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full px-5 py-3.5 rounded-xl bg-gray-900/50 border border-white/10 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all shadow-inner" placeholder="••••••••">
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-purple-500 focus:ring-purple-500 focus:ring-offset-gray-950 cursor-pointer">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-400 cursor-pointer">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit" class="w-full py-4 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold text-lg shadow-[0_0_20px_rgba(168,85,247,0.3)] hover:shadow-[0_0_30px_rgba(168,85,247,0.5)] hover:-translate-y-1 transition-all duration-300">
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-gray-400 relative z-10 border-t border-white/10 pt-6">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-bold transition-colors">Daftar secara gratis</a>
            </div>
        </div>
    </div>
</body>
</html>
