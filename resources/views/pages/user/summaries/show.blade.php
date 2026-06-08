<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">{{ __('ui.summary_detail_kicker') }}</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">{{ $summary->title }}</h2>
            <p class="mt-2 text-sm text-slate-300/80">{{ __('ui.source') }}: {{ $summary->material?->title ?? __('ui.without_material') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        @if (session('warning'))
            <div class="rounded-2xl border border-yellow-500/30 bg-yellow-500/10 p-4 text-sm text-yellow-100">{{ session('warning') }}</div>
        @endif

        <section class="feature-hero">
            <div class="max-w-3xl">
                <p class="user-kicker text-[11px] text-cyan-100/90">{{ __('ui.summary_context_kicker') }}</p>
                <p class="mt-3 text-sm text-slate-100/80">{{ __('ui.summary_context_description') }}</p>
            </div>
        </section>

        <section class="glass-panel accent-card-cyan rounded-[1.75rem] p-6">
            <div class="mb-5 flex flex-wrap gap-3 text-sm text-slate-300/75">
                <span>{{ __('ui.model') }}: {{ $summary->model ?? '-' }}</span>
                <span>{{ __('ui.owner') }}: {{ $summary->user?->name ?? '-' }}</span>
                @if ($summary->material)
                    <a href="{{ route('materials.show', $summary->material) }}" class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-cyan-100">{{ __('ui.open_material') }}</a>
                @endif
            </div>
            <div class="glass-panel rounded-[1.5rem] p-5 text-sm leading-7 text-slate-100 whitespace-pre-line">{{ $summary->summary_text }}</div>
        </section>
    </div>
</x-app-layout>
