<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pricing - Nalarin.ai</title>
        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-outfit { font-family: 'Outfit', sans-serif; }
            .font-inter { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="font-inter bg-white text-slate-950 antialiased selection:bg-sky-200">
        <x-page-loader />

        <header class="relative overflow-hidden rounded-b-[2rem] bg-gradient-to-br from-sky-50 via-white to-cyan-100">
            <div class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:px-10">
                <nav class="flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-flex items-center">
                        <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-9 w-auto max-w-[190px] object-contain sm:h-10" alt="Nalarin.ai Logo">
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="hidden items-center gap-8 text-sm font-semibold text-slate-700 md:flex">
                            <a href="{{ url('/#fitur') }}" class="transition hover:text-sky-600">Fitur</a>
                            <a href="{{ route('pricing') }}" class="text-sky-600">Harga</a>
                            <a href="{{ url('/#testimoni') }}" class="transition hover:text-sky-600">Testimoni</a>
                        </div>
                        <a href="{{ route('login') }}" class="hidden rounded-lg px-4 py-2 text-sm font-bold text-slate-700 transition hover:text-sky-600 sm:inline-flex">Login</a>
                        <a href="{{ route('login') }}" class="inline-flex rounded-lg bg-sky-500 px-4 py-2 text-sm font-bold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-600">Masuk</a>
                    </div>
                </nav>

                <div class="grid min-h-[560px] items-center gap-10 py-12 lg:grid-cols-[0.95fr_1.05fr] lg:py-16">
                    <section class="max-w-2xl">
                        <h1 class="font-outfit text-4xl font-extrabold leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            Transformasi Cara Belajarmu dengan Kecerdasan Buatan
                        </h1>
                        <p class="mt-6 max-w-xl text-base leading-7 text-slate-700 sm:text-lg">
                            Satu platform untuk semua kebutuhan belajarmu. Upload materi apapun, dapatkan ringkasan, flashcard, dan kuis dalam sekejap tanpa repot.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-sky-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
                                Mulai Belajar Sekarang
                            </a>
                            <a href="#pricing" class="inline-flex items-center justify-center rounded-lg border border-sky-700/50 bg-white/60 px-6 py-3 text-sm font-bold text-sky-900 transition hover:bg-white">
                                Lihat Paket
                            </a>
                        </div>
                    </section>

                    <section class="relative min-h-[360px] overflow-hidden rounded-[2rem] bg-cyan-50/50 lg:min-h-[460px]">
                        <div class="absolute left-10 top-16 rounded-2xl border border-sky-200 bg-white/80 p-3 shadow-sm">
                            <svg class="h-12 w-12 text-sky-500" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                                <rect x="7" y="10" width="34" height="26" rx="4" fill="#E0F2FE" stroke="currentColor" stroke-width="2"/>
                                <path d="M14 18h12M14 24h8M30 29l4-5 5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="absolute right-8 top-10 rounded-2xl border border-cyan-200 bg-white/80 px-4 py-3 text-2xl font-extrabold text-cyan-600 shadow-sm">AI</div>
                        <div class="absolute right-14 top-36 rounded-2xl border border-sky-200 bg-white/80 p-3 shadow-sm">
                            <svg class="h-10 w-10 text-sky-500" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                                <path d="M24 7v6M24 35v6M7 24h6M35 24h6M12 12l4 4M32 32l4 4M36 12l-4 4M16 32l-4 4" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="24" cy="24" r="8" fill="#BAE6FD" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <img src="{{ asset('images/NALA.png') }}" class="absolute bottom-0 left-1/2 h-[340px] w-auto -translate-x-1/2 object-contain sm:h-[420px] lg:h-[470px]" alt="Nalarin.ai AI assistant">
                    </section>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mt-14">
                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Free</p>
                    <h2 class="font-outfit text-3xl font-bold mt-3">Rp0</h2>
                    <p class="text-sm text-gray-400 mt-3">Untuk validasi produk dan onboarding awal.</p>
                    <ul class="space-y-3 mt-8 text-sm text-gray-200">
                        <li>Upload materi dan summary dasar</li>
                        <li>Flashcards dan kuis otomatis</li>
                        <li>Maksimal 2 room dibuat</li>
                        <li>Kuota <em>study matching</em> terbatas</li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 inline-block rounded-xl bg-white text-gray-950 px-5 py-3 font-semibold">Mulai Gratis</a>
                </section>
                <section class="rounded-[2rem] border border-purple-500/30 bg-gradient-to-br from-purple-600/20 to-blue-600/10 p-8">
                    <p class="text-xs uppercase tracking-[0.2em] text-purple-300">Premium</p>
                    <h2 class="font-outfit text-3xl font-bold mt-3">Rp99.000<span class="text-lg text-gray-300">/bulan</span></h2>
                    <p class="text-sm text-gray-300 mt-3">Untuk bisnis edukasi, komunitas belajar, dan social study retention.</p>
                    <ul class="space-y-3 mt-8 text-sm text-gray-100">
                        <li>Ruang kelas lebih banyak</li>
                        <li><em>Study matching</em> tanpa batas</li>
                        <li>Prioritas social features</li>
                        <li>Upsell yang siap dipasang ke funnel penjualan</li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 inline-block rounded-xl bg-purple-500 px-5 py-3 font-semibold">Upgrade Interest</a>
                </section>
            </div>
        </footer>
    </body>
</html>
