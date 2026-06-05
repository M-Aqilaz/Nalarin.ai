<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>404 - Nalarin.ai</title>
        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|poppins:500,600,700,800|roboto:400,500,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="user-theme font-inter antialiased text-gray-100 overflow-x-hidden selection:bg-purple-500/30">
        <x-page-loader />

        <div class="page-orb top-20 right-[12%] h-64 w-64 bg-cyan-400/20"></div>
        <div class="page-orb bottom-12 left-[10%] h-72 w-72 bg-fuchsia-500/20"></div>

        <main class="relative flex min-h-screen items-center justify-center px-4 py-12">
            <section class="glass-panel-strong user-highlight-ring w-full max-w-3xl overflow-hidden rounded-[2rem] p-8 text-center sm:p-12">
                <div class="mx-auto flex max-w-2xl flex-col items-center">
                    <img src="{{ asset('images/favicon.png') }}" alt="Nalarin.ai" class="h-28 w-28 animate-[loader-float_2.6s_ease-in-out_infinite] object-contain sm:h-32 sm:w-32">

                    <p class="user-kicker mt-6 text-[11px] text-cyan-100/90">Page Not Found</p>
                    <h1 class="mt-3 font-outfit text-5xl font-bold soft-gradient-text sm:text-6xl">404</h1>
                    <h2 class="mt-4 font-outfit text-2xl font-semibold text-white sm:text-3xl">Halaman yang kamu cari tidak ditemukan.</h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300/80 sm:text-base">
                        Bisa jadi link-nya sudah berubah, halamannya sudah dipindahkan, atau URL yang dimasukkan belum tepat. Kembali ke beranda atau dashboard untuk melanjutkan.
                    </p>

                    <div class="mt-8 flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                        <a href="{{ url('/') }}" class="user-primary-button px-6 py-3 text-sm">Kembali ke Beranda</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-2xl border border-white/10 bg-white/[0.08] px-6 py-3 text-sm font-medium text-white transition hover:bg-white/[0.14]">Buka Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-2xl border border-white/10 bg-white/[0.08] px-6 py-3 text-sm font-medium text-white transition hover:bg-white/[0.14]">Masuk ke Akun</a>
                        @endauth
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
