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
                        <x-language-switch class="rounded-xl bg-white hover:border-sky-300" />
                        <a href="{{ route('login') }}" class="inline-flex h-11 w-28 items-center justify-center rounded-xl bg-sky-500 text-sm font-bold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-600 hover:shadow-lg">Masuk</a>
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

                    <section class="relative min-h-[420px] overflow-hidden rounded-[2rem] bg-white lg:min-h-[540px]">
                        <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-cyan-100/70 to-transparent"></div>
                        <img src="{{ asset('images/nala_teacher.png') }}" class="absolute inset-0 h-full w-full object-contain" alt="Nala, AI assistant Nalarin.ai">
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
                </div>

                @php
                    $features = [
                        ['title' => 'Ringkasan Otomatis', 'desc' => 'Ubah materi panjang menjadi poin penting yang mudah dipahami.', 'tone' => 'text-sky-600', 'icon' => 'summary'],
                        ['title' => 'AI Tutor 24/7', 'desc' => 'Tanyakan konsep sulit dan dapatkan penjelasan yang lebih sederhana.', 'tone' => 'text-cyan-600', 'icon' => 'chat'],
                        ['title' => 'Smart Flashcard', 'desc' => 'Buat kartu hafalan otomatis dari materi yang kamu unggah.', 'tone' => 'text-pink-500', 'icon' => 'cards'],
                        ['title' => 'Interactive Quiz', 'desc' => 'Latih pemahaman lewat kuis yang dibuat dari materi belajarmu.', 'tone' => 'text-emerald-500', 'icon' => 'quiz'],
                        ['title' => 'Pomodoro Fokus', 'desc' => 'Atur sesi belajar dan istirahat agar fokus tetap terjaga.', 'tone' => 'text-orange-500', 'icon' => 'pomodoro'],
                        ['title' => 'Focus Planner', 'desc' => 'Susun target dan agenda belajar harian secara lebih terarah.', 'tone' => 'text-amber-500', 'icon' => 'planner'],
                        ['title' => 'Study Matching', 'desc' => 'Temukan partner belajar dengan tujuan dan minat yang sesuai.', 'tone' => 'text-rose-500', 'icon' => 'matching'],
                        ['title' => 'Room Kelas', 'desc' => 'Belajar dan berdiskusi bersama dalam ruang kelas virtual.', 'tone' => 'text-violet-500', 'icon' => 'room'],
                    ];
                @endphp

                <div
                    class="feature-marquee mt-12"
                    x-data="{ paused: false }"
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                    @touchstart.passive="paused = true"
                    @touchend.passive="paused = false"
                    @touchcancel.passive="paused = false"
                    aria-label="Fitur Nalarin.ai"
                >
                    <div class="feature-marquee-track" :class="{ 'is-paused': paused }">
                        @foreach (array_merge($features, $features) as $index => $feature)
                            <article class="flex min-h-[260px] w-[280px] shrink-0 flex-col justify-between rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm sm:w-[300px]" @if ($index >= count($features)) aria-hidden="true" @endif>
                                <div>
                                    <div class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm {{ $feature['tone'] }}">
                                        @if ($feature['icon'] === 'summary')
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M7 3.75h7.5L19 8.25v12H7z" stroke-linejoin="round"/>
                                                <path d="M14.5 3.75v4.5H19M10 12h6M10 15h6M10 18h4" stroke-linecap="round"/>
                                            </svg>
                                        @elseif ($feature['icon'] === 'chat')
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M5 5.5h14v10H9l-4 3v-13Z" stroke-linejoin="round"/>
                                                <path d="M8.5 10.5h.01M12 10.5h.01M15.5 10.5h.01" stroke-width="2.5" stroke-linecap="round"/>
                                            </svg>
                                        @elseif ($feature['icon'] === 'cards')
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <rect x="6" y="5" width="12" height="14" rx="2"/>
                                                <path d="M9 2.75h8a3 3 0 0 1 3 3v10M4 8.25v8a3 3 0 0 0 3 3M9.5 10h5M9.5 13h5" stroke-linecap="round"/>
                                            </svg>
                                        @elseif ($feature['icon'] === 'quiz')
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <rect x="5" y="3.5" width="14" height="17" rx="2"/>
                                                <path d="m8.5 9 1.5 1.5L13 7.5M8.5 15l1.5 1.5 3-3M15 9h1M15 15h1" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        @elseif ($feature['icon'] === 'pomodoro')
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <circle cx="12" cy="13" r="7.5"/>
                                                <path d="M9 3h6M12 5.5V8M12 13l3-2M17.5 7.5 19 6" stroke-linecap="round"/>
                                            </svg>
                                        @elseif ($feature['icon'] === 'planner')
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <rect x="4" y="5.5" width="16" height="14" rx="2"/>
                                                <path d="M8 3.5v4M16 3.5v4M4 9.5h16M8 13h3M8 16h7" stroke-linecap="round"/>
                                            </svg>
                                        @elseif ($feature['icon'] === 'matching')
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <circle cx="8" cy="9" r="3"/>
                                                <circle cx="16.5" cy="8" r="2.5"/>
                                                <path d="M3.5 19c.5-3.2 2-5 4.5-5s4 1.8 4.5 5M13 14c2.8-.8 5.8.7 6.5 4" stroke-linecap="round"/>
                                            </svg>
                                        @else
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M4 5.5h16v11H9l-5 3v-14Z" stroke-linejoin="round"/>
                                                <path d="M8 9.5h8M8 12.5h5" stroke-linecap="round"/>
                                            </svg>
                                        @endif
                                    </div>
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
                </div>

                @php
                    $testimonials = [
                        ['name' => 'Alya Ramadhani', 'role' => 'Siswa SMA', 'text' => 'Materi langsung jadi ringkasan dan flashcard, jadi belajar sebelum ujian jauh lebih cepat.'],
                        ['name' => 'Rizky Maulana', 'role' => 'Mahasiswa', 'text' => 'Kuis dan Tutor AI bikin saya bukan cuma baca materi, tapi benar-benar paham.'],
                        ['name' => 'Dina Prasetyo', 'role' => 'Mentor Belajar', 'text' => 'Materi lebih rapi, latihan lebih terarah, dan diskusi belajar jadi lebih aktif.'],
                        ['name' => 'Nadia Putri', 'role' => 'Siswa Kelas 12', 'text' => 'Pomodoro dan planner membantu saya konsisten belajar tanpa merasa kewalahan.'],
                        ['name' => 'Fajar Nugraha', 'role' => 'Mahasiswa Teknik', 'text' => 'Ringkasan otomatis sangat membantu saat harus memahami banyak modul dalam waktu singkat.'],
                        ['name' => 'Sarah Amelia', 'role' => 'Tutor Privat', 'text' => 'Flashcard dan kuisnya cocok untuk mengulang materi bersama murid secara lebih menarik.'],
                    ];
                @endphp

                <div
                    class="testimonial-marquee mt-10"
                    x-data="{ paused: false }"
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                    @touchstart.passive="paused = true"
                    @touchend.passive="paused = false"
                    @touchcancel.passive="paused = false"
                    aria-label="Testimoni pengguna Nalarin.ai"
                >
                    <div class="testimonial-marquee-track" :class="{ 'is-paused': paused }">
                        @foreach (array_merge($testimonials, $testimonials) as $index => $testimonial)
                            <article class="min-h-[220px] w-[300px] shrink-0 rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm sm:w-[380px]" @if ($index >= count($testimonials)) aria-hidden="true" @endif>
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

            <section class="relative overflow-hidden bg-[linear-gradient(90deg,#ffffff_0%,#ffffff_24%,#f0f9ff_42%,#cffafe_100%)]">
                <div class="mx-auto grid min-h-[390px] max-w-7xl items-end px-5 sm:px-8 lg:grid-cols-[360px_minmax(0,1fr)] lg:px-10 xl:grid-cols-[390px_minmax(0,1fr)]">
                    <div class="hidden h-full items-end justify-center lg:flex">
                        <img src="{{ asset('images/nala_cta.png') }}" class="pointer-events-none h-[360px] w-[360px] object-cover object-top xl:h-[390px] xl:w-[390px]" alt="Nala guide">
                    </div>
                    <div class="self-center py-20 text-center lg:px-8">
                        <h2 class="font-outfit text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Siap Revolusi Cara Belajarmu?</h2>
                        <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-700">
                            Satu platform untuk semua kebutuhan belajarmu. Upload materi apapun, dapatkan ringkasan, flashcard, dan kuis dalam sekejap.
                        </p>
                        <a href="{{ route('login') }}" class="mt-8 inline-flex items-center justify-center rounded-lg bg-sky-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
                            Masuk Ruang Belajar
                        </a>
                    </div>
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
