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

            @if ($summary->material)
                @php
                    $material = $summary->material;
                    $readableCharacters = number_format(mb_strlen((string) $material->raw_text), 0, ',', '.');
                    $engineName = match ($material->ocr_engine) {
                        'pdftotext' => 'PDF lokal',
                        'native' => 'Ekstraktor dokumen lokal',
                        'openrouter-pdf' => 'AI fallback PDF',
                        'openrouter-vision' => 'AI vision',
                        'tesseract' => 'OCR lokal',
                        default => $material->original_filename ? 'Ekstraktor file' : 'Teks manual',
                    };
                @endphp

                <div class="mb-5 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-sm text-emerald-50">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-extrabold text-white">Materi berhasil dibaca</p>
                            <p class="mt-1 text-xs leading-5 text-emerald-100/80">
                                {{ $readableCharacters }} karakter teks terbaca lewat {{ $engineName }}.
                            </p>
                        </div>
                        <span class="inline-flex w-fit rounded-full border border-emerald-300/30 bg-white/10 px-3 py-1 text-xs font-bold text-emerald-50">
                            {{ $material->status ?? 'processed' }}
                        </span>
                    </div>

                    @if ($material->ocr_warning)
                        <div class="mt-3 rounded-xl border border-yellow-300/25 bg-yellow-300/10 px-3 py-2 text-xs leading-5 text-yellow-50">
                            {{ $material->ocr_warning }}
                        </div>
                    @endif
                </div>
            @endif

            @php
                $summaryHtml = \Illuminate\Support\Str::markdown((string) $summary->summary_text, [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ]);
            @endphp

            <article class="summary-rendered rounded-[1.5rem] border border-sky-100 bg-white p-5 shadow-sm sm:p-7">
                {!! $summaryHtml !!}
            </article>
        </section>
    </div>
</x-app-layout>
