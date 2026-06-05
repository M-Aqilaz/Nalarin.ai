<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Nalarin.ai') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|poppins:500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-inter text-slate-950 antialiased">
        <x-page-loader />
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-sky-50 via-white to-cyan-100 px-4">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-sky-600" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 overflow-hidden rounded-3xl border border-sky-200 bg-white/92 px-6 py-5 shadow-[0_20px_45px_rgba(14,116,144,0.14)]">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
