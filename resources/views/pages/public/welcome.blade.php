<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nalarin.ai - Platform Belajar AI untuk Siswa Indonesia</title>
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

        <!-- Navbar -->
        <nav class="fixed w-full z-50 glass-card border-b-0 border-white/5 top-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/logo_nalarin_ai.png') }}" class="w-8 h-8 object-contain" alt="Nalarin.ai Logo">
                        <span class="font-outfit font-bold text-2xl tracking-tight text-white">Nalarin<span class="text-purple-400">.ai</span></span>
                    </div>
                    <div class="hidden md:flex space-x-8">
                        <a href="#fitur" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Fitur</a>
                        <a href="{{ route('pricing') }}" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Harga</a>
                        <a href="#testimoni" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Testimoni</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center px-3 py-1.5 bg-white/5 rounded-full border border-white/10" x-data="{ lang: 'id' }">
                            <span class="text-xs font-semibold mr-2" :class="lang === 'id' ? 'text-white' : 'text-gray-500'">ID</span>
                            <button @click="lang = lang === 'id' ? 'en' : 'id'" class="relative inline-flex h-4 w-8 items-center rounded-full transition-colors focus:outline-none" :class="lang === 'en' ? 'bg-purple-500' : 'bg-gray-600'">
                                <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform" :class="lang === 'en' ? 'translate-x-4' : 'translate-x-1'"></span>
                            </button>
                            <span class="text-xs font-semibold ml-2" :class="lang === 'en' ? 'text-white' : 'text-gray-500'">EN</span>
                        </div>
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full bg-white text-gray-950 text-sm font-semibold hover:bg-gray-200 transition shadow-[0_0_20px_rgba(255,255,255,0.2)]">Mulai Belajar</a>
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
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-sky-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
                                Mulai Belajar Sekarang
                            </a>
                            <a href="#fitur" class="inline-flex items-center justify-center rounded-lg border border-sky-700/50 bg-white/60 px-6 py-3 text-sm font-bold text-sky-900 transition hover:bg-white">
                                Lihat Demo
                            </a>
                        </div>
                    </section>

                    <section class="relative min-h-[420px] overflow-hidden rounded-[2rem] bg-cyan-50/50 lg:min-h-[540px]">
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
                        <img src="{{ asset('images/nala_body.png') }}" class="absolute bottom-0 left-1/2 h-[360px] w-auto max-w-none -translate-x-1/2 object-contain sm:h-[430px] lg:h-[500px]" alt="Nala, AI assistant Nalarin.ai">
                    </section>
                </div>
            </div>
        </header>

<<<<<<< Updated upstream
        <main>
            <section id="fitur" class="py-20 sm:py-24">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        Belajar Lebih Cerdas, Bukan Lebih Keras
                    </h2>
=======
        <!-- Hero Section -->
        <main class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden items-center flex flex-col justify-center min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-purple-500/30 bg-purple-500/10 text-purple-300 text-xs font-medium mb-8">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                    </span>
                    Nalarin.ai V2.0 Kini Tersedia
                </div>
                
                <h1 class="font-outfit text-5xl md:text-7xl font-extrabold tracking-tight text-white mb-6 leading-tight">
                    Transformasi Cara Belajarmu <br />
                    dengan <span class="gradient-text">Kecerdasan Buatan</span>
                </h1>
                
                <p class="mt-4 max-w-2xl text-lg md:text-xl text-gray-400 mx-auto mb-10 leading-relaxed">
                    Satu platform untuk semua kebutuhan belajarmu. Unggah materi apapun, dapatkan ringkasan, <em>flashcard</em>, dan kuis dalam sekejap tanpa repot.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('feature.upload') }}" class="px-8 py-4 rounded-full bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold text-lg hover:shadow-[0_0_30px_rgba(168,85,247,0.4)] hover:scale-105 transition-all duration-300">
                        Mulai Belajar Sekarang
                    </a>
                    <a href="#demo" class="px-8 py-4 rounded-full border border-gray-600 bg-gray-800/50 text-white font-semibold text-lg hover:bg-gray-700/50 transition-all duration-300 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Lihat Demo
                    </a>
                </div>
>>>>>>> Stashed changes

                    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <article class="flex min-h-[320px] flex-col justify-between rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                            <div>
                                <div class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm">
                                    <svg class="h-10 w-10 text-sky-600" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                                        <path d="M13 6h16l7 7v29H13V6Z" fill="#E0F2FE" stroke="currentColor" stroke-width="2"/>
                                        <path d="M29 6v8h7M18 22h13M18 28h13M18 34h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-extrabold text-slate-950">Ringkasan Otomatis</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-700">Ringkasan otomatis, ringkasan, dan dalam rens report.</p>
                            </div>
                            <div class="mt-8 rounded-2xl bg-white/70 p-4">
                                <span class="text-3xl font-extrabold text-sky-500">AI</span>
                            </div>
                        </article>

                        <article class="flex min-h-[320px] flex-col justify-between rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                            <div>
                                <div class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm">
                                    <svg class="h-10 w-10 text-sky-600" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                                        <path d="M10 16a9 9 0 0 1 9-9h10a9 9 0 0 1 9 9v4a9 9 0 0 1-9 9h-4l-8 7v-7a9 9 0 0 1-7-9v-4Z" fill="#BAE6FD" stroke="currentColor" stroke-width="2"/>
                                        <path d="M18 18h.01M24 18h.01M30 18h.01" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-extrabold text-slate-950">AI Tutor 24/7</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-700">AI Tutor 24/7 diajath materi korboat ai chat kemetihn.</p>
                            </div>
                            <div class="mt-8 flex items-end justify-center">
                                <div class="rounded-2xl bg-white/70 p-4">
                                    <svg class="h-16 w-16 text-sky-500" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                        <rect x="14" y="22" width="36" height="28" rx="9" fill="#E0F2FE" stroke="currentColor" stroke-width="3"/>
                                        <path d="M32 22v-8M24 14h16" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                        <circle cx="25" cy="36" r="3" fill="currentColor"/>
                                        <circle cx="39" cy="36" r="3" fill="currentColor"/>
                                        <path d="M26 44h12" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>
                        </article>

                        <article class="flex min-h-[320px] flex-col justify-between rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                            <div>
                                <div class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm">
                                    <svg class="h-10 w-10 text-pink-500" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                                        <rect x="12" y="10" width="24" height="28" rx="5" fill="#FCE7F3" stroke="currentColor" stroke-width="2"/>
                                        <path d="M18 18c2-5 10-5 12 0M18 30c2 5 10 5 12 0M17 24h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-extrabold text-slate-950">Smart Flashcards</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-700">Flashcard dam roanasian yang memkusci untuk memparoh hatilin.</p>
                            </div>
                            <div class="mt-8 flex justify-center">
                                <div class="rounded-2xl bg-white/70 p-4">
                                    <svg class="h-16 w-16 text-pink-500" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                        <path d="M25 16c-7 0-12 5-12 12 0 4 2 7 5 9-1 6 4 11 10 11h2V16h-5Z" fill="#FBCFE8" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
                                        <path d="M39 16c7 0 12 5 12 12 0 4-2 7-5 9 1 6-4 11-10 11h-2V16h5Z" fill="#FBCFE8" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
                                        <path d="M22 26h8M20 36h10M34 26h8M34 36h10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>
                        </article>

                        <article class="flex min-h-[320px] flex-col justify-between rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                            <div>
                                <div class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm">
                                    <svg class="h-12 w-12 text-emerald-500" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                                        <circle cx="24" cy="24" r="16" fill="#D1FAE5" stroke="currentColor" stroke-width="2"/>
                                        <path d="m16 24 6 6 12-14" stroke="#059669" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-extrabold text-slate-950">Interactive Quiz</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-700">Interactive Quiz upomnemonic quiz yang dalam ai suarnya.</p>
                            </div>
                            <div class="mt-8 flex justify-center">
                                <div class="rounded-full bg-white/70 p-5">
                                    <svg class="h-16 w-16 text-emerald-500" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                                        <circle cx="24" cy="24" r="18" fill="#CCFBF1" stroke="currentColor" stroke-width="2"/>
                                        <path d="m15 24 6 6 13-14" stroke="#059669" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

<<<<<<< Updated upstream
            <section id="testimoni" class="py-16 sm:py-20">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        Dipakai dan Disukai Siswa
                    </h2>
                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        @foreach ([
                            ['name' => 'Nania A.', 'avatar' => 'NA', 'text' => '"Nalarin.ai tutau Belajarmu. Sosiai pangant belinrokan menshama yang adakan mingunu Bios-eadah tinanis.vii aoah ko:neluritar?"'],
                            ['name' => 'Niokak', 'avatar' => 'NK', 'text' => '"Rembanyan hizerann sediah soah menyatikan menjaaiakan ringkasan. Siswa istu, menkantan ter capat dan podukkin-i-ro-maximal."'],
                            ['name' => 'Jasao H.', 'avatar' => 'JH', 'text' => '"Fkon sava littis ai belajarmu n:manian dan omanin dilngkan menghasai nennori kelarau sesanual adakah bereka Jutani/oron tumjat."'],
                        ] as $testimonial)
                            <article class="rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-sm font-extrabold text-sky-700 shadow-sm">{{ $testimonial['avatar'] }}</div>
                                    <div>
                                        <h3 class="font-bold text-slate-950">{{ $testimonial['name'] }}</h3>
                                        <div class="text-sm text-amber-400">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                                    </div>
                                </div>
                                <p class="mt-4 text-sm leading-6 text-slate-700">{{ $testimonial['text'] }}</p>
                            </article>
                        @endforeach
=======
        <!-- Features Section -->
        <section id="fitur" class="py-24 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="font-outfit text-3xl md:text-5xl font-bold text-white mb-4">Belajar Lebih Cerdas, Bukan Lebih Keras</h2>
                    <p class="text-gray-400 text-lg">Semua alat yang kamu butuhkan untuk memahami materi dengan cepat dan menyenangkan.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Feature 1 -->
                    <div class="glass-card p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-500/20 transition-colors border border-blue-500/20">
                            <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 font-outfit">Ringkasan Otomatis</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Ubah dokumen ratusan halaman menjadi poin-poin penting yang mudah dipahami dalam hitungan detik.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="glass-card p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-500/20 transition-colors border border-purple-500/20">
                            <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 font-outfit">AI Tutor 24/7</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Tanyakan apa saja tentang materimu. AI kami siap menjelaskan konsep sulit kapan saja layaknya guru pribadi.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="glass-card p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="w-14 h-14 bg-pink-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-pink-500/20 transition-colors border border-pink-500/20">
                            <svg class="w-7 h-7 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 font-outfit"><em>Smart Flashcards</em></h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Hafalkan istilah penting dengan metode Spaced Repetition yang dibuktikan secara ilmiah efektif.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="glass-card p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="w-14 h-14 bg-green-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-500/20 transition-colors border border-green-500/20">
                            <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 font-outfit">Kuis Interaktif</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Uji pemahamanmu dengan kuis pilihan ganda yang dibuat otomatis dari materi yang kamu pelajari.</p>
>>>>>>> Stashed changes
                    </div>
                </div>
            </section>

<<<<<<< Updated upstream
            <section class="relative overflow-hidden bg-gradient-to-br from-sky-50 via-white to-cyan-100 py-20 text-center">
                <img src="{{ asset('images/nala_halfbody.png') }}" class="pointer-events-none absolute bottom-0 left-8 hidden h-28 w-48 object-cover object-top opacity-95 lg:block" alt="Nala guide">
                <div class="mx-auto max-w-3xl px-5">
                    <h2 class="font-outfit text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Siap Revolusi Cara Belajarmu?</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-700">
                        Satu platform untuk semua kebutuhan belajarmu. Upload materi apapun, dapatkan ringkasan, flashcard, ia repot.
                    </p>
                    <a href="{{ route('login') }}" class="mt-8 inline-flex items-center justify-center rounded-lg bg-sky-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
=======
        <section id="testimoni" class="py-24 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="font-outfit text-3xl md:text-5xl font-bold text-white mb-4">Dipakai dan Disukai Siswa</h2>
                    <p class="text-gray-400 text-lg">Testimoni awal untuk memperkuat kepercayaan, positioning produk, dan konversi landing page.</p>
                </div>

                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <article class="glass-card p-8 rounded-3xl">
                        <div class="flex items-center gap-1 text-amber-300 text-sm">
                            <span>★★★★★</span>
                        </div>
                        <p class="mt-5 text-gray-200 leading-7">
                            “Biasanya aku butuh waktu lama buat bikin rangkuman sendiri. Di Nalarin.ai, materi langsung jadi ringkasan dan <em>flashcard</em>, jadi belajar sebelum ujian jauh lebih cepat.”
                        </p>
                        <div class="mt-6">
                            <p class="font-semibold text-white">Alya Ramadhani</p>
                            <p class="text-sm text-gray-400">Siswa SMA, Jakarta</p>
                        </div>
                    </article>

                    <article class="glass-card p-8 rounded-3xl">
                        <div class="flex items-center gap-1 text-amber-300 text-sm">
                            <span>★★★★★</span>
                        </div>
                        <p class="mt-5 text-gray-200 leading-7">
                            “Fitur kuis dan Tutor AI-nya bikin aku nggak cuma baca materi, tapi benar-benar ngerti. Cocok buat persiapan presentasi dan tugas harian.”
                        </p>
                        <div class="mt-6">
                            <p class="font-semibold text-white">Rizky Maulana</p>
                            <p class="text-sm text-gray-400">Mahasiswa Semester 3</p>
                        </div>
                    </article>

                    <article class="glass-card p-8 rounded-3xl">
                        <div class="flex items-center gap-1 text-amber-300 text-sm">
                            <span>★★★★★</span>
                        </div>
                        <p class="mt-5 text-gray-200 leading-7">
                            “Anak-anak di komunitas belajar kami lebih aktif diskusi setelah pakai platform seperti ini. Materi lebih rapi, latihan lebih terarah, dan engagement naik.”
                        </p>
                        <div class="mt-6">
                            <p class="font-semibold text-white">Dina Prasetyo</p>
                            <p class="text-sm text-gray-400">Mentor Komunitas Belajar</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 relative z-10">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-br from-purple-900/50 to-blue-900/50 border border-purple-500/20 rounded-[3rem] p-12 text-center relative overflow-hidden glass-card">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
                    <h2 class="font-outfit text-3xl md:text-5xl font-bold text-white mb-6 relative z-10">Siap Revolusi Cara Belajarmu?</h2>
                    <p class="text-xl text-purple-200 mb-10 max-w-2xl mx-auto relative z-10">Gabung dengan ribuan siswa cerdas lainnya. Mulai gratis, upgrade kapan saja. Tidak perlu kartu kredit.</p>
                    <a href="{{ route('feature.upload') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-full bg-white text-gray-900 font-bold text-lg hover:bg-gray-100 hover:scale-105 transition-all duration-300 shadow-[0_0_40px_rgba(255,255,255,0.3)] relative z-10">
>>>>>>> Stashed changes
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
