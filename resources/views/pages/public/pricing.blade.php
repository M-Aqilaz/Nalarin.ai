<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('ui.pricing_meta_title') }}</title>
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
        @php
            $proAmount = (int) config('services.pakasir.plans.pro_monthly.amount', 49000);
            $ultimateAmount = (int) config('services.pakasir.plans.ultimate_yearly.amount', 490000);
        @endphp

        <header class="relative overflow-hidden rounded-b-[2rem] bg-gradient-to-br from-sky-50 via-white to-cyan-100">
            <div class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:px-10">
                <nav class="flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-flex items-center">
                        <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-9 w-auto max-w-[190px] object-contain sm:h-10" alt="Nalarin.ai Logo">
                    </a>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('pricing') }}" class="hidden rounded-xl px-4 py-2 text-sm font-bold text-sky-600 transition hover:bg-white/70 sm:inline-flex">{{ __('ui.landing_pricing') }}</a>
                        <x-language-switch />
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 min-w-[7rem] items-center justify-center rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex min-h-11 min-w-[7rem] items-center justify-center rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-600">{{ __('ui.landing_login') }}</a>
                        @endauth
                    </div>
                </nav>

                <div class="grid min-h-[460px] items-center gap-10 py-10 lg:grid-cols-[0.92fr_1.08fr] lg:py-12">
                    <section class="max-w-2xl">
                        <p class="inline-flex rounded-full border border-sky-200 bg-white/80 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.18em] text-sky-700 shadow-sm">
                            {{ __('ui.pricing_badge') }}
                        </p>
                        <h1 class="font-outfit text-4xl font-extrabold leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            {{ __('ui.pricing_hero_title') }}
                        </h1>
                        <p class="mt-6 max-w-xl text-base leading-7 text-slate-700 sm:text-lg">
                            {{ __('ui.pricing_hero_description') }}
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="#pricing" class="inline-flex items-center justify-center rounded-lg border border-sky-700/50 bg-white/60 px-6 py-3 text-sm font-bold text-sky-900 transition hover:bg-white">
                                {{ __('ui.pricing_compare') }}
                            </a>
                            <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-sky-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
                                {{ __('ui.pricing_start_free') }}
                            </a>
                        </div>
                        <div class="mt-8 grid max-w-xl gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-sky-200 bg-white/75 p-4 shadow-sm">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Basic</p>
                                <p class="mt-1 text-xl font-extrabold text-slate-950">{{ __('ui.pricing_free') }}</p>
                            </div>
                            <div class="rounded-2xl border border-purple-200 bg-white/75 p-4 shadow-sm">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Premium</p>
                                <p class="mt-1 text-xl font-extrabold text-slate-950">Rp {{ number_format($proAmount, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl border border-teal-200 bg-white/75 p-4 shadow-sm">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Ultimate</p>
                                <p class="mt-1 text-xl font-extrabold text-slate-950">Rp {{ number_format($ultimateAmount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="relative hidden min-h-[380px] overflow-hidden rounded-[2rem] bg-white/45 lg:block">
                        <div class="absolute left-8 top-8 w-72 rounded-2xl border border-purple-200 bg-white/90 p-5 shadow-lg shadow-purple-200/30">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-extrabold text-slate-950">{{ __('ui.pricing_recommended') }}</p>
                                <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold text-purple-700">Premium</span>
                            </div>
                            <p class="mt-4 text-3xl font-extrabold text-slate-950">Rp {{ number_format($proAmount, 0, ',', '.') }}<span class="text-base font-bold text-slate-500">{{ __('ui.pricing_per_month') }}</span></p>
                            <div class="mt-5 space-y-3 text-sm font-semibold text-slate-700">
                                <p>&#10003; AI Tutor 24/7</p>
                                <p>&#10003; {{ __('ui.pricing_unlimited_flashcards_quizzes') }}</p>
                                <p>&#10003; {{ __('ui.pricing_priority_support') }}</p>
                            </div>
                        </div>
                        <div class="absolute right-8 top-12 rounded-2xl border border-teal-200 bg-white/90 px-5 py-4 shadow-md">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('ui.pricing_annual_savings') }}</p>
                            <p class="mt-1 text-xl font-extrabold text-teal-700">Ultimate</p>
                        </div>
                        <div class="absolute bottom-8 right-10 max-w-[230px] rounded-2xl border border-sky-200 bg-sky-50/90 px-5 py-4 shadow-md">
                            <p class="text-sm font-extrabold text-slate-950">{{ __('ui.pricing_upgrade_note') }}</p>
                        </div>
                        <img src="{{ asset('images/nala_dashboard_chibi.png') }}" class="absolute bottom-[-1rem] left-[55%] h-[330px] w-[330px] -translate-x-1/2 rounded-full object-cover mix-blend-multiply lg:h-[370px] lg:w-[370px]" alt="Nalarin.ai AI assistant">
                    </section>
                </div>
            </div>
        </header>

        <main>
            <section id="pricing" class="scroll-mt-6 py-16 sm:py-20">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        {{ __('ui.pricing_choose_title') }}
                    </h2>

                    @if ($errors->has('billing'))
                        <div class="mx-auto mt-8 max-w-3xl rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
                            {{ $errors->first('billing') }}
                        </div>
                    @endif

                    @if (session('billing_status'))
                        <div class="mx-auto mt-8 max-w-3xl rounded-2xl border border-cyan-200 bg-cyan-50 px-5 py-4 text-sm font-semibold text-cyan-800">
                            {{ session('billing_status') }}
                        </div>
                    @endif

                    <div class="mt-10 grid gap-6 lg:grid-cols-3">
                        <article class="overflow-hidden rounded-2xl border border-sky-300 bg-white shadow-sm">
                            <div class="bg-sky-400 p-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-semibold text-slate-900">Basic</p>
                                    <span class="rounded-full bg-white/85 px-3 py-1 text-xs font-bold text-slate-700">{{ __('ui.pricing_free_label') }}</span>
                                </div>
                                <h3 class="mt-2 text-3xl font-extrabold text-slate-950">{{ __('ui.pricing_free') }}</h3>
                            </div>
                            <div class="space-y-4 p-6 text-sm text-slate-700">
                                <p>&#10003; {{ __('ui.pricing_auto_summary') }}</p>
                                <p>&#10003; {{ __('ui.pricing_ten_flashcards') }}</p>
                                <p>&#10003; {{ __('ui.pricing_limited_access') }}</p>
                                <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-lg border border-sky-700/60 px-5 py-3 font-bold text-sky-800 transition hover:bg-sky-50">{{ __('ui.pricing_choose_plan') }}</a>
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-2xl border border-purple-300 bg-white shadow-lg shadow-purple-200/50">
                            <div class="bg-purple-300 p-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-semibold text-slate-900">Premium</p>
                                    <span class="rounded-full bg-purple-600/70 px-3 py-1 text-xs font-bold text-white">{{ __('ui.pricing_most_popular') }}</span>
                                </div>
                                <h3 class="mt-2 text-3xl font-extrabold text-slate-950">Rp {{ number_format($proAmount, 0, ',', '.') }}<span class="text-lg font-semibold">{{ __('ui.pricing_per_month') }}</span></h3>
                            </div>
                            <div class="space-y-4 p-6 text-sm text-slate-700">
                                <p>&#10003; {{ __('ui.pricing_all_basic_features') }}</p>
                                <p>&#10003; AI Tutor 24/7</p>
                                <p>&#10003; {{ __('ui.pricing_monthly_match_credits', ['count' => 99]) }}</p>
                                <p>&#10003; {{ __('ui.pricing_unlimited_flashcard_quiz') }}</p>
                                <p>&#10003; {{ __('ui.pricing_priority_support') }}</p>
                                @auth
                                    <form method="POST" action="{{ route('billing.checkout', 'pro_monthly') }}" class="mt-8">
                                        @csrf
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-purple-500 px-5 py-3 font-bold text-white shadow-lg shadow-purple-400/30 transition hover:bg-purple-600">{{ __('ui.pricing_pay_pakasir') }}</button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-lg bg-purple-500 px-5 py-3 font-bold text-white shadow-lg shadow-purple-400/30 transition hover:bg-purple-600">{{ __('ui.pricing_login_to_buy') }}</a>
                                @endauth
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-2xl border border-teal-300 bg-white shadow-sm">
                            <div class="bg-teal-400 p-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-semibold text-slate-900">Ultimate</p>
                                    <span class="rounded-full bg-white/85 px-3 py-1 text-xs font-bold text-slate-700">{{ __('ui.pricing_annual') }}</span>
                                </div>
                                <h3 class="mt-2 text-3xl font-extrabold text-slate-950">Rp {{ number_format($ultimateAmount, 0, ',', '.') }}<span class="text-lg font-semibold">{{ __('ui.pricing_per_year') }}</span></h3>
                            </div>
                            <div class="space-y-4 p-6 text-sm text-slate-700">
                                <p>&#10003; {{ __('ui.pricing_all_premium_features') }}</p>
                                <p>&#10003; {{ __('ui.pricing_yearly_match_credits', ['count' => 999]) }}</p>
                                <p>&#10003; {{ __('ui.pricing_complete_analytics') }}</p>
                                <p>&#10003; {{ __('ui.pricing_exclusive_content') }}</p>
                                <p>&#10003; {{ __('ui.pricing_save_twenty') }}</p>
                                @auth
                                    <form method="POST" action="{{ route('billing.checkout', 'ultimate_yearly') }}" class="mt-8">
                                        @csrf
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-teal-500 px-5 py-3 font-bold text-white shadow-lg shadow-teal-400/30 transition hover:bg-teal-600">{{ __('ui.pricing_pay_pakasir') }}</button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-lg bg-teal-500 px-5 py-3 font-bold text-white shadow-lg shadow-teal-400/30 transition hover:bg-teal-600">{{ __('ui.pricing_login_to_buy') }}</a>
                                @endauth
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="py-16 sm:py-20">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <h2 class="font-outfit text-center text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        {{ __('ui.pricing_benefits_title') }}
                    </h2>

                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            ['title' => __('ui.landing_feature_summary_title'), 'text' => __('ui.pricing_benefit_summary_description'), 'icon' => 'doc'],
                            ['title' => __('ui.landing_feature_tutor_title'), 'text' => __('ui.pricing_benefit_tutor_description'), 'icon' => 'chat'],
                            ['title' => __('ui.landing_feature_flashcard_title'), 'text' => __('ui.pricing_benefit_flashcard_description'), 'icon' => 'brain'],
                            ['title' => __('ui.landing_feature_quiz_title'), 'text' => __('ui.pricing_benefit_quiz_description'), 'icon' => 'quiz'],
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

            <section class="bg-gradient-to-br from-sky-50 via-white to-cyan-100 py-20 text-center">
                <div class="mx-auto max-w-3xl px-5">
                    <h2 class="font-outfit text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">{{ __('ui.pricing_cta_title') }}</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-700">
                        {{ __('ui.pricing_cta_description') }}
                    </p>
                    <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="mt-8 inline-flex items-center justify-center rounded-lg bg-sky-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">
                        {{ __('ui.landing_enter_learning_space') }}
                    </a>
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
