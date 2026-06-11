<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('ui.landing_meta_title') }}</title>
        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-outfit { font-family: 'Outfit', sans-serif; }
            .font-inter { font-family: 'Inter', sans-serif; }

            .landing-page,
            .landing-page a,
            .landing-page button,
            .landing-page [role="button"] {
                cursor: url("{{ asset('images/nala_cursor_chibi.png') }}") 12 3, auto !important;
            }

            .landing-page .feature-marquee,
            .landing-page .testimonial-marquee {
                position: relative;
                overflow: hidden;
                padding-block: 1.25rem 1.75rem;
                mask-image: linear-gradient(to right, transparent, #000 7%, #000 93%, transparent);
                -webkit-mask-image: linear-gradient(to right, transparent, #000 7%, #000 93%, transparent);
            }

            .landing-page .feature-marquee::before,
            .landing-page .feature-marquee::after,
            .landing-page .testimonial-marquee::before,
            .landing-page .testimonial-marquee::after {
                content: "";
                position: absolute;
                top: 1rem;
                bottom: 1.5rem;
                z-index: 2;
                width: min(8rem, 16vw);
                pointer-events: none;
            }

            .landing-page .feature-marquee::before,
            .landing-page .testimonial-marquee::before {
                left: 0;
                background: linear-gradient(to right, #ffffff, rgba(255, 255, 255, 0));
            }

            .landing-page .feature-marquee::after,
            .landing-page .testimonial-marquee::after {
                right: 0;
                background: linear-gradient(to left, #ffffff, rgba(255, 255, 255, 0));
            }

            .landing-page .feature-marquee-track,
            .landing-page .testimonial-marquee-track {
                display: flex !important;
                width: max-content !important;
                gap: 1.5rem;
                will-change: transform;
                transform: translate3d(0, 0, 0);
            }

            .landing-page .feature-marquee-track {
                animation: landing-feature-scroll 36s linear infinite !important;
            }

            .landing-page .testimonial-marquee-track {
                animation: landing-testimonial-scroll 42s linear infinite !important;
            }

            .landing-page .feature-marquee:hover .feature-marquee-track,
            .landing-page .feature-marquee-track.is-paused,
            .landing-page .testimonial-marquee:hover .testimonial-marquee-track,
            .landing-page .testimonial-marquee-track.is-paused {
                animation-play-state: paused !important;
            }

            .landing-carousel-card {
                position: relative;
                overflow: hidden;
                isolation: isolate;
                transition: transform 260ms ease, box-shadow 260ms ease, border-color 260ms ease;
            }

            .landing-carousel-card > * {
                position: relative;
                z-index: 1;
            }

            .landing-carousel-card::before {
                content: "";
                position: absolute;
                inset: 0;
                z-index: 0;
                background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.2), transparent 36%),
                    linear-gradient(120deg, transparent 0%, rgba(255, 255, 255, 0.78) 45%, transparent 70%);
                opacity: 0;
                transform: translateX(-30%);
                transition: opacity 260ms ease, transform 520ms ease;
                pointer-events: none;
            }

            .landing-carousel-card:hover {
                border-color: rgba(14, 165, 233, 0.52);
                box-shadow: 0 22px 45px rgba(14, 165, 233, 0.16);
                transform: translateY(-8px) scale(1.015);
            }

            .landing-carousel-card:hover::before {
                opacity: 1;
                transform: translateX(30%);
            }

            .landing-carousel-icon {
                animation: landing-card-float 3.8s ease-in-out infinite;
            }

            .landing-carousel-card:nth-child(2n) .landing-carousel-icon {
                animation-delay: -1.4s;
            }

            .landing-carousel-card:nth-child(3n) .landing-carousel-icon {
                animation-delay: -2.2s;
            }

            .landing-spark {
                position: absolute;
                right: 1rem;
                top: 1rem;
                height: 0.65rem;
                width: 0.65rem;
                border-radius: 9999px;
                background: #38bdf8;
                box-shadow: 0 0 0 7px rgba(56, 189, 248, 0.14);
                animation: landing-spark-pulse 2.6s ease-in-out infinite;
            }

            @keyframes landing-card-float {
                0%, 100% { transform: translateY(0) rotate(0deg); }
                50% { transform: translateY(-7px) rotate(2deg); }
            }

            @keyframes landing-spark-pulse {
                0%, 100% { opacity: 0.42; transform: scale(0.9); }
                50% { opacity: 1; transform: scale(1.18); }
            }

            @keyframes landing-feature-scroll {
                from { transform: translate3d(0, 0, 0); }
                to { transform: translate3d(calc(-50% - 0.75rem), 0, 0); }
            }

            @keyframes landing-testimonial-scroll {
                from { transform: translate3d(0, 0, 0); }
                to { transform: translate3d(calc(-50% - 0.75rem), 0, 0); }
            }

            @media (max-width: 768px), (pointer: coarse) {
                .landing-page,
                .landing-page a,
                .landing-page button,
                .landing-page [role="button"] {
                    cursor: auto;
                }

                .landing-page .feature-marquee,
                .landing-page .testimonial-marquee {
                    overflow-x: auto;
                    mask-image: none;
                    -webkit-mask-image: none;
                }

                .landing-page .feature-marquee-track,
                .landing-page .testimonial-marquee-track,
                .landing-carousel-icon,
                .landing-spark {
                    animation: none;
                }
            }
        </style>
    </head>
    <body class="landing-page font-inter bg-white text-slate-950 antialiased selection:bg-sky-200">
        <x-page-loader />

        <header class="relative overflow-hidden rounded-b-[2rem] bg-gradient-to-br from-sky-50 via-white to-cyan-100">
            <div class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:px-10">
                <nav class="flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-flex items-center">
                        <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-9 w-auto max-w-[190px] object-contain sm:h-10" alt="Nalarin.ai Logo">
                    </a>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('pricing') }}" class="hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-white/70 hover:text-sky-600 sm:inline-flex">
                            {{ __('ui.landing_pricing') }}
                        </a>
                        <x-language-switch />
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 min-w-[7rem] items-center justify-center rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-600">
                                {{ app()->getLocale() === 'en' ? 'Dashboard' : 'Dashboard' }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex min-h-11 min-w-[7rem] items-center justify-center rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-600">
                                {{ __('ui.landing_login') }}
                            </a>
                        @endauth
                    </div>
                </nav>

                <div class="grid min-h-[560px] items-center gap-10 py-12 lg:grid-cols-[0.95fr_1.05fr] lg:py-16">
                    <section class="max-w-2xl">
                        <h1 class="font-outfit text-4xl font-extrabold leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            {{ __('ui.landing_hero_title') }}
                        </h1>
                        <p class="mt-6 max-w-xl text-base leading-7 text-slate-700 sm:text-lg">
                            {{ __('ui.landing_hero_description') }}
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-sky-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
                                {{ __('ui.landing_start_learning') }}
                            </a>
                            <a href="#fitur" class="inline-flex items-center justify-center rounded-lg border border-sky-700/50 bg-white/60 px-6 py-3 text-sm font-bold text-sky-900 transition hover:bg-white">
                                {{ __('ui.landing_view_demo') }}
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
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2">
                            <img src="{{ asset('images/nala_teacher.png') }}" class="animate-nala-float h-[360px] w-auto max-w-none object-contain sm:h-[400px] lg:h-[500px]" alt="Nala, AI assistant Nalarin.ai">
                        </div>
                    </section>
                </div>
            </div>
        </header>

        <main>
            <section id="fitur" class="py-20 sm:py-24">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        {{ __('ui.landing_features_title') }}
                    </h2>
                </div>

                @php
                    $features = [
                        ['title' => __('ui.landing_feature_summary_title'), 'desc' => __('ui.landing_feature_summary_description'), 'tone' => 'text-sky-600', 'icon' => 'summary'],
                        ['title' => __('ui.landing_feature_tutor_title'), 'desc' => __('ui.landing_feature_tutor_description'), 'tone' => 'text-cyan-600', 'icon' => 'chat'],
                        ['title' => __('ui.landing_feature_flashcard_title'), 'desc' => __('ui.landing_feature_flashcard_description'), 'tone' => 'text-pink-500', 'icon' => 'cards'],
                        ['title' => __('ui.landing_feature_quiz_title'), 'desc' => __('ui.landing_feature_quiz_description'), 'tone' => 'text-emerald-500', 'icon' => 'quiz'],
                        ['title' => __('ui.landing_feature_pomodoro_title'), 'desc' => __('ui.landing_feature_pomodoro_description'), 'tone' => 'text-orange-500', 'icon' => 'pomodoro'],
                        ['title' => __('ui.landing_feature_planner_title'), 'desc' => __('ui.landing_feature_planner_description'), 'tone' => 'text-amber-500', 'icon' => 'planner'],
                        ['title' => __('ui.landing_feature_matching_title'), 'desc' => __('ui.landing_feature_matching_description'), 'tone' => 'text-rose-500', 'icon' => 'matching'],
                        ['title' => __('ui.landing_feature_room_title'), 'desc' => __('ui.landing_feature_room_description'), 'tone' => 'text-violet-500', 'icon' => 'room'],
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
                    aria-label="{{ __('ui.landing_features_label') }}"
                >
                    <div class="feature-marquee-track" :class="{ 'is-paused': paused }">
                        @foreach (array_merge($features, $features) as $index => $feature)
                            <article class="landing-carousel-card flex min-h-[260px] w-[280px] shrink-0 flex-col justify-between rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm sm:w-[300px]" @if ($index >= count($features)) aria-hidden="true" @endif>
                                <span class="landing-spark"></span>
                                <div>
                                    <div class="landing-carousel-icon mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm {{ $feature['tone'] }}">
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
                        {{ __('ui.landing_testimonials_title') }}
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

                    if (app()->getLocale() === 'en') {
                        $testimonials = array_merge($testimonials, [
                            ['name' => 'Emily Carter', 'role' => 'High School Student', 'text' => 'The summaries and flashcards help me review difficult lessons much faster before exams.'],
                            ['name' => 'Daniel Lee', 'role' => 'University Student', 'text' => 'AI Tutor explains complex topics clearly, while the quizzes help me check what I truly understand.'],
                            ['name' => 'Sophie Martin', 'role' => 'Private Tutor', 'text' => 'Nalarin.ai makes lesson reviews more structured and engaging for my students.'],
                        ]);
                    }
                @endphp

                <div
                    class="testimonial-marquee mt-10"
                    x-data="{ paused: false }"
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                    @touchstart.passive="paused = true"
                    @touchend.passive="paused = false"
                    @touchcancel.passive="paused = false"
                    aria-label="{{ __('ui.landing_testimonials_label') }}"
                >
                    <div class="testimonial-marquee-track" :class="{ 'is-paused': paused }">
                        @foreach (array_merge($testimonials, $testimonials) as $index => $testimonial)
                            <article class="landing-carousel-card min-h-[220px] w-[300px] shrink-0 rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm sm:w-[380px]" @if ($index >= count($testimonials)) aria-hidden="true" @endif>
                                <span class="landing-spark"></span>
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
                        <h2 class="font-outfit text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">{{ __('ui.landing_cta_title') }}</h2>
                        <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-700">
                            {{ __('ui.landing_cta_description') }}
                        </p>
                        <a href="{{ route('login') }}" class="mt-8 inline-flex items-center justify-center rounded-lg bg-sky-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
                            {{ __('ui.landing_enter_learning_space') }}
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-gradient-to-br from-sky-50 via-white to-cyan-100">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 border-t border-sky-200 px-5 py-8 sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10">
                <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-9 w-auto max-w-[190px] object-contain" alt="Nalarin.ai Logo">
                <p class="text-sm font-medium text-slate-700">&copy; {{ date('Y') }} Nalarin.ai. {{ __('ui.landing_footer_rights') }}</p>
            </div>
        </footer>
    </body>
</html>
