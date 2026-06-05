@props([
    'variant' => 'half',
    'size' => 'md',
    'mood' => 'happy',
    'alt' => 'Nala',
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

    $sources = [
        'half' => 'images/nala_halfbody.png',
        'full' => 'images/nala_body.png',
        'face' => $faces[$mood] ?? $faces['happy'],
    ];

    $source = $sources[$variant] ?? $sources['half'];
    $sizeClasses = [
        'sm' => [
            'half' => 'h-28 w-44',
            'full' => 'h-48 w-36',
            'face' => 'h-20 w-20',
        ],
        'md' => [
            'half' => 'h-36 w-56',
            'full' => 'h-64 w-44',
            'face' => 'h-28 w-28',
        ],
        'lg' => [
            'half' => 'h-44 w-64',
            'full' => 'h-80 w-56',
            'face' => 'h-36 w-36',
        ],
        'hero' => [
            'half' => 'h-48 w-72',
            'full' => 'h-[22rem] w-64',
            'face' => 'h-44 w-44',
        ],
    ];

    $frameClass = $sizeClasses[$size][$variant] ?? $sizeClasses['md'][$variant] ?? $sizeClasses['md']['half'];
    $fitClass = $variant === 'face' ? 'object-contain p-1' : 'object-cover object-center';
@endphp

<div {{ $attributes->merge(['class' => "relative overflow-hidden {$frameClass}"]) }}>
    <img src="{{ asset($source) }}" alt="{{ $alt }}" class="absolute inset-0 h-full w-full {{ $fitClass }} drop-shadow-[0_18px_28px_rgba(14,116,144,0.18)]">
</div>
