<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Daftar Materi</h2>
            <p class="text-sm text-gray-400 mt-1">Semua materi belajarmu yang sudah tersimpan dan siap dipakai ulang.</p>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('feature.upload') }}" class="inline-flex w-full items-center justify-center px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-medium text-sm transition md:w-auto">Tambah Materi</a>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($materials as $material)
            <a href="{{ route('materials.show', $material) }}" class="glass-panel rounded-2xl border border-white/5 p-5 hover:bg-white/5 transition block">
                <div class="flex items-start justify-between gap-4">
                    <p class="text-lg font-outfit font-semibold text-white leading-snug">{{ $material->title }}</p>
                    <span class="shrink-0 rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-[11px] uppercase tracking-wide text-gray-300">{{ $material->status }}</span>
                </div>
                <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-400">
                    <span class="rounded-full bg-white/5 px-3 py-1">{{ $material->summaries_count }} ringkasan</span>
                    <span class="rounded-full bg-white/5 px-3 py-1">{{ $material->chat_threads_count }} thread</span>
                </div>
            </a>
        @empty
            <div class="glass-panel rounded-2xl border border-white/5 p-5 text-sm text-gray-400 md:col-span-2 xl:col-span-3">Belum ada materi. Buat dari halaman upload.</div>
        @endforelse
    </div>
</x-app-layout>
