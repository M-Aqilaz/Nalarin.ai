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
        </header>

        <main>
            <section class="py-20 sm:py-24">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        Belajar Lebih Cerdas, Bukan Lebih Keras
                    </h2>

                    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            ['title' => 'Ringkasan Otomatis', 'text' => 'Ringkasan otomatis, ringkasan, dan dalam rens report.', 'icon' => 'doc'],
                            ['title' => 'AI Tutor 24/7', 'text' => 'AI Tutor 24/7 diajath materi korboat ai chat kemetihn.', 'icon' => 'chat'],
                            ['title' => 'Smart Flashcards', 'text' => 'Flashcard dam roanasian yang memkusci untuk memparoh hatilin.', 'icon' => 'brain'],
                            ['title' => 'Interactive Quiz', 'text' => 'Interactive Quiz upomnemonic quiz yang dalam ai suarnya.', 'icon' => 'quiz'],
                        ] as $feature)
                            <article class="rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                                <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-3xl shadow-sm">
                                    @if ($feature['icon'] === 'doc')
                                        <span class="font-extrabold text-sky-500">AI</span>
                                    @elseif ($feature['icon'] === 'chat')
                                        <span class="text-base font-extrabold text-sky-600">CHAT</span>
                                    @elseif ($feature['icon'] === 'brain')
                                        <span class="text-base font-extrabold text-pink-500">CARD</span>
                                    @else
                                        <span class="text-4xl font-extrabold text-emerald-500">&#10003;</span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-extrabold text-slate-950">{{ $feature['title'] }}</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-700">{{ $feature['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="pricing" class="pb-20">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        Pilih Paket Harga
                    </h2>

                    <div class="mt-10 grid gap-6 lg:grid-cols-3">
                        <article class="overflow-hidden rounded-2xl border border-sky-300 bg-white shadow-sm">
                            <div class="bg-sky-400 p-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-semibold text-slate-900">Basic</p>
                                    <span class="rounded-full bg-white/85 px-3 py-1 text-xs font-bold text-slate-700">Free</span>
                                </div>
                                <h3 class="mt-2 text-3xl font-extrabold text-slate-950">Gratis</h3>
                            </div>
                            <div class="space-y-4 p-6 text-sm text-slate-700">
                                <p>&#10003; Ringkasan Otomatis</p>
                                <p>&#10003; 10 Smart Flashcards/bulan</p>
                                <p>&#10003; Akses terbatas</p>
                                <a href="{{ route('register') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-lg border border-sky-700/60 px-5 py-3 font-bold text-sky-800 transition hover:bg-sky-50">Pilih Paket</a>
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-2xl border border-purple-300 bg-white shadow-lg shadow-purple-200/50">
                            <div class="bg-purple-300 p-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-semibold text-slate-900">Pro</p>
                                    <span class="rounded-full bg-purple-600/70 px-3 py-1 text-xs font-bold text-white">Most Popular</span>
                                </div>
                                <h3 class="mt-2 text-3xl font-extrabold text-slate-950">Rp 49rb<span class="text-lg font-semibold">/bulan</span></h3>
                            </div>
                            <div class="space-y-4 p-6 text-sm text-slate-700">
                                <p>&#10003; Semua fitur Basic</p>
                                <p>&#10003; AI Tutor 24/7</p>
                                <p>&#10003; Unlimited Flashcards & Quiz</p>
                                <p>&#10003; Prioritas Dukungan</p>
                                <a href="{{ route('register') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-lg bg-purple-500 px-5 py-3 font-bold text-white shadow-lg shadow-purple-400/30 transition hover:bg-purple-600">Pilih Paket</a>
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-2xl border border-teal-300 bg-white shadow-sm">
                            <div class="bg-teal-400 p-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-semibold text-slate-900">Ultimate</p>
                                    <span class="rounded-full bg-white/85 px-3 py-1 text-xs font-bold text-slate-700">Annual</span>
                                </div>
                                <h3 class="mt-2 text-3xl font-extrabold text-slate-950">Rp 490rb<span class="text-lg font-semibold">/tahun</span></h3>
                            </div>
                            <div class="space-y-4 p-6 text-sm text-slate-700">
                                <p>&#10003; Semua fitur Pro</p>
                                <p>&#10003; Analisis Belajar Lengkap</p>
                                <p>&#10003; Konten Eksklusif</p>
                                <p>&#10003; Hemat 20%</p>
                                <a href="{{ route('register') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-lg bg-teal-500 px-5 py-3 font-bold text-white shadow-lg shadow-teal-400/30 transition hover:bg-teal-600">Pilih Paket</a>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="bg-gradient-to-br from-sky-50 via-white to-cyan-100 py-20 text-center">
                <div class="mx-auto max-w-3xl px-5">
                    <h2 class="font-outfit text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Siap Revolusi Cara Belajarmu?</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-700">
                        Satu platform untuk semua kebutuhan belajarmu. Upload materi apapun, dapatkan ringkasan, flashcard, ia repot.
                    </p>
                    <a href="{{ route('register') }}" class="mt-8 inline-flex items-center justify-center rounded-lg bg-sky-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
                        Masuk Ruang Belajar
                    </a>
                </div>
            </section>
        </main>

        <footer class="bg-gradient-to-br from-sky-50 via-white to-cyan-100">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 border-t border-sky-200 px-5 py-8 sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10">
                <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-9 w-auto max-w-[190px] object-contain" alt="Nalarin.ai Logo">
                <p class="text-sm font-medium text-slate-700">&copy; Copyright All. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>
