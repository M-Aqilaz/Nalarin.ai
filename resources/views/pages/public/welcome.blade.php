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

        <header class="relative overflow-hidden rounded-b-[2rem] bg-gradient-to-br from-sky-50 via-white to-cyan-100">
            <div class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:px-10">
                <nav class="flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-flex items-center">
                        <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-9 w-auto max-w-[190px] object-contain sm:h-10" alt="Nalarin.ai Logo">
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="hidden items-center gap-8 text-sm font-semibold text-slate-700 md:flex">
                            <a href="#fitur" class="transition hover:text-sky-600">Fitur</a>
                            <a href="{{ route('pricing') }}" class="transition hover:text-sky-600">Harga</a>
                            <a href="#testimoni" class="transition hover:text-sky-600">Testimoni</a>
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

        <main>
            <section id="fitur" class="py-20 sm:py-24">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        Belajar Lebih Cerdas, Bukan Lebih Keras
                    </h2>
                    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            ['title' => 'Ringkasan Otomatis', 'desc' => 'Ubah materi panjang menjadi poin penting yang mudah dipahami.', 'tone' => 'text-sky-600', 'icon' => 'AI'],
                            ['title' => 'AI Tutor 24/7', 'desc' => 'Tanyakan konsep sulit dan dapatkan penjelasan yang lebih sederhana.', 'tone' => 'text-cyan-600', 'icon' => 'QA'],
                            ['title' => 'Smart Flashcards', 'desc' => 'Buat kartu hafalan otomatis dari materi yang kamu unggah.', 'tone' => 'text-pink-500', 'icon' => 'FC'],
                            ['title' => 'Interactive Quiz', 'desc' => 'Latih pemahaman lewat kuis yang dibuat dari materi belajarmu.', 'tone' => 'text-emerald-500', 'icon' => 'OK'],
                        ] as $feature)
                            <article class="flex min-h-[260px] flex-col justify-between rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                                <div>
                                    <div class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-xl font-extrabold shadow-sm {{ $feature['tone'] }}">{{ $feature['icon'] }}</div>
                                    <h3 class="text-lg font-extrabold text-slate-950">{{ $feature['title'] }}</h3>
                                    <p class="mt-3 text-sm leading-6 text-slate-700">{{ $feature['desc'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="testimoni" class="py-16 sm:py-20">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        Dipakai dan Disukai Siswa
                    </h2>
                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        @foreach ([
                            ['name' => 'Alya Ramadhani', 'role' => 'Siswa SMA', 'text' => 'Materi langsung jadi ringkasan dan flashcard, jadi belajar sebelum ujian jauh lebih cepat.'],
                            ['name' => 'Rizky Maulana', 'role' => 'Mahasiswa', 'text' => 'Kuis dan Tutor AI bikin saya bukan cuma baca materi, tapi benar-benar paham.'],
                            ['name' => 'Dina Prasetyo', 'role' => 'Mentor Belajar', 'text' => 'Materi lebih rapi, latihan lebih terarah, dan diskusi belajar jadi lebih aktif.'],
                        ] as $testimonial)
                            <article class="rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                                <div class="text-sm text-amber-400">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                                <p class="mt-4 text-sm leading-6 text-slate-700">"{{ $testimonial['text'] }}"</p>
                                <div class="mt-5">
                                    <h3 class="font-bold text-slate-950">{{ $testimonial['name'] }}</h3>
                                    <p class="text-sm text-slate-600">{{ $testimonial['role'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="relative overflow-hidden bg-gradient-to-br from-sky-50 via-white to-cyan-100 py-20 text-center">
                <img src="{{ asset('images/nala_halfbody.png') }}" class="pointer-events-none absolute bottom-0 left-8 hidden h-28 w-48 object-cover object-top opacity-95 lg:block" alt="Nala guide">
                <div class="mx-auto max-w-3xl px-5">
                    <h2 class="font-outfit text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Siap Revolusi Cara Belajarmu?</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-700">
                        Satu platform untuk semua kebutuhan belajarmu. Upload materi apapun, dapatkan ringkasan, flashcard, dan kuis dalam sekejap.
                    </p>
                    <a href="{{ route('login') }}" class="mt-8 inline-flex items-center justify-center rounded-lg bg-sky-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
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
