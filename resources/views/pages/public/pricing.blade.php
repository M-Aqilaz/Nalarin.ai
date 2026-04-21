<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pricing - Nalarin.ai</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-950 text-white font-inter min-h-screen">
        <div class="max-w-6xl mx-auto px-4 py-16">
            <a href="{{ url('/') }}" class="text-sm text-purple-300">← Kembali</a>
            <div class="text-center mt-10">
                <h1 class="font-outfit text-5xl font-bold">Pricing untuk Produk Belajar AI</h1>
                <p class="text-gray-400 mt-4 max-w-2xl mx-auto">Struktur awal freemium untuk menjual Nalarin.ai dengan kombinasi AI learning dan fitur sosial.</p>
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
                        <li>Kuota study matching terbatas</li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 inline-block rounded-xl bg-white text-gray-950 px-5 py-3 font-semibold">Mulai Gratis</a>
                </section>
                <section class="rounded-[2rem] border border-purple-500/30 bg-gradient-to-br from-purple-600/20 to-blue-600/10 p-8">
                    <p class="text-xs uppercase tracking-[0.2em] text-purple-300">Premium</p>
                    <h2 class="font-outfit text-3xl font-bold mt-3">Rp99.000<span class="text-lg text-gray-300">/bulan</span></h2>
                    <p class="text-sm text-gray-300 mt-3">Untuk bisnis edukasi, komunitas belajar, dan social study retention.</p>
                    <ul class="space-y-3 mt-8 text-sm text-gray-100">
                        <li>Room kelas lebih banyak</li>
                        <li>Study matching tanpa batas</li>
                        <li>Prioritas social features</li>
                        <li>Upsell yang siap dipasang ke funnel penjualan</li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 inline-block rounded-xl bg-purple-500 px-5 py-3 font-semibold">Upgrade Interest</a>
                </section>
            </div>
        </div>
    </body>
</html>
