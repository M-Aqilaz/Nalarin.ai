@props([
    'mood' => 'happy',
    'title' => 'Nala siap bantu',
    'message' => 'Kalau bingung mulai dari mana, ikuti langkah kecil berikutnya dulu. Jangan cuma dilihatin, ya.',
    'actionLabel' => null,
    'actionUrl' => null,
    'compact' => false,
])

@php
    $faces = [
        'happy' => 'images/nalaFaces/nala_mentahan-happy.png',
        'flat' => 'images/nalaFaces/nala_mentahan-flat.png',
        'angry' => 'images/nalaFaces/nala_mentahan-angry.png',
        'sad' => 'images/nalaFaces/nala_mentahan-sad.png',
        'cute' => 'images/nalaFaces/nala_mentahan-cute.png',
        'shy' => 'images/nalaFaces/nala_mentahan-shy.png',
        'silly' => 'images/nalaFaces/nala_mentahan-silly.png',
        'sorry' => 'images/nalaFaces/nala_mentahan-sorry.png',
    ];

    $face = $faces[$mood] ?? $faces['happy'];
    $imageClass = $compact ? 'h-24 w-24 sm:h-28 sm:w-28' : 'h-32 w-32 sm:h-40 sm:w-40';
@endphp

<section {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-[1.75rem] border border-sky-200 bg-gradient-to-br from-white/92 via-sky-50/90 to-cyan-100/85 p-4 text-slate-950 shadow-[0_18px_38px_rgba(14,116,144,0.14)] backdrop-blur']) }}>
    <div class="pointer-events-none absolute -right-10 -top-12 h-32 w-32 rounded-full bg-cyan-200/55 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-14 left-8 h-28 w-28 rounded-full bg-sky-200/60 blur-3xl"></div>

    <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="mx-auto flex shrink-0 items-end justify-center sm:mx-0">
            <img src="{{ asset($face) }}" alt="Nala" class="{{ $imageClass }} object-contain drop-shadow-[0_18px_28px_rgba(14,116,144,0.2)]">
        </div>

        <div class="min-w-0 flex-1 text-center sm:text-left">
            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Nala Guide</p>
            <h3 class="mt-2 font-outfit text-xl font-extrabold leading-tight text-slate-950 sm:text-2xl">{{ $title }}</h3>
            <p class="mt-2 text-sm leading-6 text-slate-700">{{ $message }}</p>
        </div>

        @if ($actionLabel && $actionUrl)
            <div class="sm:shrink-0">
                <a href="{{ $actionUrl }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-500 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600 sm:w-auto">
                    {{ $actionLabel }}
                </a>
            </div>
        @endif
    </div>
</section>
