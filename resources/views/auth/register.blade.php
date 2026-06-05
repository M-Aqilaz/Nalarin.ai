<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - Nalarin.ai</title>
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
            <aside class="relative hidden min-h-[650px] overflow-hidden bg-gradient-to-br from-sky-100 via-white to-cyan-100 p-8 lg:block">
                <a href="{{ url('/') }}" class="inline-flex items-center">
                    <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-10 w-auto max-w-[200px] object-contain" alt="Nalarin.ai Logo">
                </a>

                <div class="mx-auto mt-8 flex h-[270px] w-[330px] items-center justify-center">
                    <img src="{{ asset('images/NALA.png') }}" class="max-h-full w-auto object-contain drop-shadow-[0_24px_40px_rgba(14,116,144,0.22)]" alt="Nala">
                </div>

                <div class="mt-8 max-w-sm">
                    <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-sky-700">Nalarin.ai</p>
                    <h1 class="mt-4 font-outfit text-4xl font-extrabold leading-tight text-slate-950">Buat akun, biar Nala bisa mulai bantu belajarmu.</h1>
                    <p class="mt-5 text-base leading-7 text-slate-700">Simpan materi, buat ringkasan, latihan kuis, dan cari partner belajar dari satu workspace yang rapi.</p>
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
                    <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-sky-700">Register</p>
                    <h2 class="mt-3 font-outfit text-3xl font-extrabold text-slate-950">Buat akun baru</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Mulai pengalaman belajar cerdasmu sekarang secara gratis.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Nama Lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-2xl border border-sky-200 bg-white px-5 py-3.5 text-slate-950 placeholder-slate-400 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Alamat Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-2xl border border-sky-200 bg-white px-5 py-3.5 text-slate-950 placeholder-slate-400 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="nama@email.com">
                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-bold text-slate-700">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-2xl border border-sky-200 bg-white px-5 py-3.5 text-slate-950 placeholder-slate-400 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-2xl border border-sky-200 bg-white px-5 py-3.5 text-slate-950 placeholder-slate-400 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Ulangi password">
                        @error('password_confirmation')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-sky-500 px-5 py-4 text-base font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:-translate-y-0.5 hover:bg-sky-600">
                        Daftar
                    </button>
                </form>

                <div class="mt-7 rounded-2xl border border-sky-100 bg-sky-50 px-4 py-4 text-center text-sm font-semibold text-slate-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-extrabold text-sky-700 transition hover:text-sky-500">Masuk di sini</a>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
