<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Ringkasan Materi</h2>
            <p class="text-sm text-gray-400 mt-1">Daftar seluruh hasil ringkasan dasar yang tersimpan.</p>
        </div>
    </x-slot>

    <div class="space-y-4">
        @forelse ($summaries as $summary)
            <a href="{{ route('summaries.show', $summary) }}" class="glass-panel rounded-2xl border border-white/5 p-5 hover:bg-white/5 transition block">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-lg font-outfit font-semibold text-white">{{ $summary->title }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $summary->material?->title ?? 'Tanpa materi' }} • {{ $summary->model ?? 'Tanpa model' }}</p>
                    </div>
                    <span class="text-sm text-purple-400">Lihat</span>
                </div>
            </a>
        @empty
            <div class="glass-panel rounded-2xl border border-white/5 p-5 text-sm text-gray-400">Belum ada ringkasan.</div>
        @endforelse
    </div>
</x-app-layout>
