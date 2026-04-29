<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">{{ $summary->title }}</h2>
            <p class="text-sm text-gray-400 mt-1">Sumber: {{ $summary->material?->title ?? 'Tanpa materi' }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        @if (session('warning'))
            <div class="rounded-2xl border border-yellow-500/30 bg-yellow-500/10 p-4 text-sm text-yellow-100">{{ session('warning') }}</div>
        @endif

        <section class="glass-panel rounded-2xl border border-white/5 p-6">
            <div class="flex flex-wrap gap-3 text-sm text-gray-400 mb-5">
                <span>Model: {{ $summary->model ?? '-' }}</span>
                <span>Pemilik: {{ $summary->user?->name ?? '-' }}</span>
                @if ($summary->material)
                    <a href="{{ route('materials.show', $summary->material) }}" class="text-purple-400">Buka materi</a>
                @endif
            </div>
            <div class="rounded-2xl bg-gray-900/70 border border-white/5 p-5 text-sm leading-7 text-gray-200 whitespace-pre-line">{{ $summary->summary_text }}</div>
        </section>
    </div>
</x-app-layout>
