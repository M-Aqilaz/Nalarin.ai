<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">Detail Materi</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">{{ $material->title }}</h2>
            <p class="mt-2 text-sm text-slate-300/80">Lihat isi materi dan lanjutkan belajar melalui ringkasan, kartu belajar, kuis, atau tutor AI.</p>
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
                <p class="user-kicker text-[11px] text-cyan-100/90">Materi Siap Dipelajari</p>
                <p class="mt-3 text-sm text-slate-100/80">Tulisan yang jelas membantu Nalarin.ai memberikan ringkasan dan latihan yang lebih baik.</p>
            </div>
        </section>

        <section class="glass-panel accent-card-cyan rounded-[1.75rem] p-5 md:p-6">
            <div class="grid grid-cols-1 gap-4 text-sm text-slate-200 md:grid-cols-2">
                <div class="glass-panel rounded-2xl p-4"><span class="mb-1 block text-slate-400">Status</span>{{ $material->status }}</div>
                <div class="glass-panel rounded-2xl p-4">
                    <span class="mb-1 block text-slate-400">Pemrosesan File</span>
                    {{ $material->ocr_status === 'failed' ? 'Perlu diperiksa' : 'Selesai' }}
                </div>
                <div class="glass-panel rounded-2xl p-4"><span class="mb-1 block text-slate-400">Pemilik</span>{{ $material->user->name }}</div>
                <div class="glass-panel rounded-2xl break-all p-4"><span class="mb-1 block text-slate-400">File</span>{{ $material->original_filename ?? 'Tidak ada file' }}</div>
                <div class="glass-panel rounded-2xl p-4"><span class="mb-1 block text-slate-400">Ukuran</span>{{ $material->file_size ? number_format($material->file_size) . ' byte' : '-' }}</div>
            </div>
            @if ($material->ocr_warning)
                <div class="mt-4 rounded-2xl border border-yellow-500/30 bg-yellow-500/10 p-4 text-sm text-yellow-100">{{ $material->ocr_warning }}</div>
            @endif
            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <a href="{{ route('feature.flashcards', ['material_id' => $material->id]) }}" class="accent-card-pink rounded-2xl border border-pink-500/20 p-4 transition hover:bg-pink-500/15">
                    <p class="text-xs uppercase tracking-[0.2em] text-pink-200">Kartu Belajar Pintar</p>
                    <p class="text-white font-semibold mt-2">{{ $material->flashcardDeck ? $material->flashcardDeck->card_count . ' kartu siap dipakai' : 'Belum dibuat' }}</p>
                    <p class="text-sm text-pink-100/70 mt-1">Klik kartu ini untuk membuat latihan dari materi tersebut.</p>
                </a>
                <a href="{{ route('feature.quiz', ['material_id' => $material->id]) }}" class="accent-card-emerald rounded-2xl border border-emerald-500/20 p-4 transition hover:bg-emerald-500/15">
                    <p class="text-xs uppercase tracking-[0.2em] text-emerald-200">Latihan Kuis</p>
                    <p class="text-white font-semibold mt-2">{{ $material->quizSet ? $material->quizSet->question_count . ' soal siap dipakai' : 'Belum dibuat' }}</p>
                    <p class="text-sm text-emerald-100/70 mt-1">Klik kartu ini untuk membuat soal latihan.</p>
                </a>
            </div>
            <div class="mt-6">
                <h3 class="font-outfit text-lg font-semibold text-white mb-3">Teks Materi</h3>
                <div class="glass-panel max-h-[28rem] overflow-y-auto rounded-2xl p-4 text-sm text-slate-100 whitespace-pre-line break-words">{{ $material->raw_text ?: 'Belum ada teks materi.' }}</div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <section class="glass-panel accent-card-violet overflow-hidden rounded-[1.75rem]">
                <div class="flex items-center justify-between gap-3 border-b border-white/10 p-5">
                    <h3 class="font-outfit text-lg font-semibold text-white">Ringkasan</h3>
                    <a href="{{ route('feature.summary') }}" class="text-sm text-cyan-100">Semua ringkasan</a>
                </div>
                <div class="divide-y divide-white/10">
                    @forelse ($material->summaries as $summary)
                        <a href="{{ route('summaries.show', $summary) }}" class="block p-4 transition hover:bg-white/[0.06]">
                            <p class="text-white font-medium">{{ $summary->title }}</p>
                            <p class="mt-1 text-sm text-slate-300/70">{{ \Illuminate\Support\Str::limit($summary->summary_text, 110) }}</p>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-slate-300/70">Belum ada ringkasan untuk materi ini.</div>
                    @endforelse
                </div>
            </section>

            <section class="glass-panel accent-card-pink overflow-hidden rounded-[1.75rem]">
                <div class="flex items-center justify-between gap-3 border-b border-white/10 p-5">
                    <h3 class="font-outfit text-lg font-semibold text-white">Percakapan Tutor</h3>
                    <a href="{{ route('feature.chat') }}" class="text-sm text-cyan-100">Buka percakapan</a>
                </div>
                <div class="divide-y divide-white/10">
                    @forelse ($material->chatThreads as $thread)
                        <a href="{{ route('chat.show', $thread) }}" class="block p-4 transition hover:bg-white/[0.06]">
                            <p class="text-white font-medium">{{ $thread->title }}</p>
                            <p class="mt-1 text-sm text-slate-300/70">{{ $thread->messages->count() }} pesan</p>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-slate-300/70">Belum ada percakapan untuk materi ini.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
