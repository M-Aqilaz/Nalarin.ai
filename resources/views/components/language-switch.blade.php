@props(['compact' => false])

@php($locale = app()->getLocale())

<button
    type="button"
    x-data="{ language: @js($locale) }"
    @click="
        language = language === 'id' ? 'en' : 'id';
        window.location.href = language === 'en'
            ? @js(route('locale.update', 'en'))
            : @js(route('locale.update', 'id'));
    "
    {{ $attributes->class([
        'relative inline-flex items-center border border-sky-200 bg-white/70 p-1 font-extrabold shadow-sm transition hover:bg-white',
        'h-10 w-24 rounded-xl text-[11px]' => $compact,
        'h-11 w-28 rounded-2xl text-xs' => ! $compact,
    ]) }}
    :aria-label="language === 'id' ? @js(__('ui.switch_to_en')) : @js(__('ui.switch_to_id'))"
>
    <span
        @class([
            'absolute bg-sky-100 transition-transform duration-300 ease-out',
            'h-8 w-[42px] rounded-lg' => $compact,
            'h-9 w-[50px] rounded-xl' => ! $compact,
        ])
        :class="language === 'en' ? '{{ $compact ? 'translate-x-[46px]' : 'translate-x-[54px]' }}' : 'translate-x-0'"
    ></span>
    <span class="relative z-10 flex w-1/2 justify-center" :class="language === 'id' ? 'text-sky-700' : 'text-slate-400'">ID</span>
    <span class="relative z-10 flex w-1/2 justify-center" :class="language === 'en' ? 'text-sky-700' : 'text-slate-400'">EN</span>
</button>
