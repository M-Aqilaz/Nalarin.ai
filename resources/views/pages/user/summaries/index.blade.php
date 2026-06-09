<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">{{ __('ui.summary_page_kicker') }}</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">{{ __('ui.summary_page_title') }}</h2>
            <p class="mt-2 text-sm text-slate-300/80">{{ __('ui.summary_page_description') }}</p>
        </div>
    </x-slot>

    <div class="space-y-4">
        <section class="feature-hero">
            <div class="max-w-3xl">
                <p class="user-kicker text-[11px] text-cyan-100/90">{{ __('ui.summary_benefit_kicker') }}</p>
                <h3 class="mt-3 font-outfit text-2xl font-semibold text-white">{{ __('ui.summary_benefit_title') }}</h3>
                <p class="mt-3 text-sm text-slate-100/80">{{ __('ui.summary_benefit_description') }}</p>
            </div>
        </section>

        @forelse ($summaries as $summary)
            <a href="{{ route('summaries.show', $summary) }}" class="glass-panel accent-card-cyan block rounded-[1.75rem] p-5 transition hover:-translate-y-0.5 hover:bg-white/[0.05]">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-lg font-outfit font-semibold text-white">{{ $summary->title }}</p>
                        <p class="mt-1 text-sm text-slate-300/70">{{ $summary->material?->title ?? __('ui.without_material') }} | {{ $summary->model ?? __('ui.without_model') }}</p>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-sm text-cyan-100">{{ __('ui.view') }}</span>
                </div>
            </a>
        @empty
            <div class="glass-panel rounded-[1.75rem] p-5 text-sm text-slate-300/75">{{ __('ui.no_summaries') }}</div>
        @endforelse
    </div>
</x-app-layout>
