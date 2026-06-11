<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">Materials Library</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">Daftar Materi</h2>
            <p class="mt-2 max-w-2xl text-sm text-slate-300/80">Semua materi belajarmu yang sudah tersimpan dan siap dipakai ulang untuk ringkasan, kuis, kartu belajar, dan tutor AI.</p>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('feature.upload') }}" class="user-primary-button w-full px-5 py-2.5 text-sm md:w-auto">Tambah Materi</a>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($materials as $material)
            <a href="{{ route('materials.show', $material) }}" class="glass-panel user-highlight-ring block rounded-[1.75rem] p-5 transition hover:-translate-y-0.5 hover:bg-white/[0.06]">
                <div class="flex items-start justify-between gap-4">
                    <p class="text-lg font-outfit font-semibold text-white leading-snug">{{ $material->title }}</p>
                    <span class="shrink-0 rounded-full border border-white/10 bg-white/[0.08] px-2.5 py-1 text-[11px] uppercase tracking-wide text-slate-200">{{ $material->status }}</span>
                </div>
                <div class="mt-5 flex flex-wrap gap-2 text-xs text-slate-300/75">
                    <span class="rounded-full border border-white/10 bg-white/[0.06] px-3 py-1">{{ $material->summaries_count }} ringkasan</span>
                    <span class="rounded-full border border-white/10 bg-white/[0.06] px-3 py-1">{{ $material->chat_threads_count }} percakapan</span>
                </div>
            </a>
        @empty
            <div class="glass-panel rounded-[1.75rem] p-6 text-sm text-slate-300/75 md:col-span-2 xl:col-span-3">Belum ada materi. Buat dari halaman upload.</div>
        @endforelse
    </div>
</x-app-layout>
