@props(['name', 'class' => 'h-6 w-6'])

@switch($name)
    @case('dashboard')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M4 10.5 12 4l8 6.5V20H4v-9.5Z" stroke-linejoin="round"/>
            <path d="M9.5 20v-6h5v6" stroke-linejoin="round"/>
        </svg>
        @break
    @case('upload')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14.5V20h14v-5.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('summary')
    @case('material')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M7 3.75h7.5L19 8.25v12H7z" stroke-linejoin="round"/>
            <path d="M14.5 3.75v4.5H19M10 12h6M10 15h6M10 18h4" stroke-linecap="round"/>
        </svg>
        @break
    @case('tutor')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M5 5.5h14v10H9l-4 3v-13Z" stroke-linejoin="round"/>
            <path d="M8.5 10.5h.01M12 10.5h.01M15.5 10.5h.01" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        @break
    @case('flashcards')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="6" y="5" width="12" height="14" rx="2"/>
            <path d="M9 2.75h8a3 3 0 0 1 3 3v10M4 8.25v8a3 3 0 0 0 3 3M9.5 10h5M9.5 13h5" stroke-linecap="round"/>
        </svg>
        @break
    @case('quiz')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="5" y="3.5" width="14" height="17" rx="2"/>
            <path d="m8.5 9 1.5 1.5L13 7.5M8.5 15l1.5 1.5 3-3M15 9h1M15 15h1" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('matching')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="8" cy="9" r="3"/>
            <circle cx="16.5" cy="8" r="2.5"/>
            <path d="M3.5 19c.5-3.2 2-5 4.5-5s4 1.8 4.5 5M13 14c2.8-.8 5.8.7 6.5 4" stroke-linecap="round"/>
        </svg>
        @break
    @case('room')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M4 5.5h16v11H9l-5 3v-14Z" stroke-linejoin="round"/>
            <path d="M8 9.5h8M8 12.5h5" stroke-linecap="round"/>
        </svg>
        @break
    @case('pomodoro')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="13" r="7.5"/>
            <path d="M9 3h6M12 5.5V8M12 13l3-2M17.5 7.5 19 6" stroke-linecap="round"/>
        </svg>
        @break
    @case('planner')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="4" y="5.5" width="16" height="14" rx="2"/>
            <path d="M8 3.5v4M16 3.5v4M4 9.5h16M8 13h3M8 16h7" stroke-linecap="round"/>
        </svg>
        @break
    @case('insights')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M5 19V11M12 19V5M19 19v-8" stroke-linecap="round"/>
            <path d="M3 19.5h18" stroke-linecap="round"/>
        </svg>
        @break
    @case('billing')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3.5" y="6" width="17" height="12" rx="2"/>
            <path d="M3.5 10h17M7 14h4" stroke-linecap="round"/>
        </svg>
        @break
@endswitch
