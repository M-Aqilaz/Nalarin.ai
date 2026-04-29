<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">{{ $material->title }}</h2>
            <p class="text-sm text-gray-400 mt-1">Detail materi, ringkasan terkait, dan thread chat terkait.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        @if (session('warning'))
            <div class="rounded-2xl border border-yellow-500/30 bg-yellow-500/10 p-4 text-sm text-yellow-100">{{ session('warning') }}</div>
        @endif

        <section class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-300">
                <div class="rounded-2xl bg-white/5 p-4"><span class="text-gray-500 block mb-1">Status</span>{{ $material->status }}</div>
                <div class="rounded-2xl bg-white/5 p-4"><span class="text-gray-500 block mb-1">OCR</span>{{ $material->ocr_status }}{{ $material->ocr_engine ? ' via '.$material->ocr_engine : '' }}</div>
                <div class="rounded-2xl bg-white/5 p-4"><span class="text-gray-500 block mb-1">Pemilik</span>{{ $material->user->name }}</div>
                <div class="rounded-2xl bg-white/5 p-4 break-all"><span class="text-gray-500 block mb-1">File</span>{{ $material->original_filename ?? 'Tidak ada file' }}</div>
                <div class="rounded-2xl bg-white/5 p-4"><span class="text-gray-500 block mb-1">Ukuran</span>{{ $material->file_size ? number_format($material->file_size) . ' bytes' : '-' }}</div>
            </div>
            @if ($material->ocr_warning)
                <div class="mt-4 rounded-2xl border border-yellow-500/30 bg-yellow-500/10 p-4 text-sm text-yellow-100">{{ $material->ocr_warning }}</div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <a href="{{ route('feature.flashcards', ['material_id' => $material->id]) }}" class="rounded-2xl border border-pink-500/20 bg-pink-500/10 p-4 hover:bg-pink-500/15 transition">
                    <p class="text-xs uppercase tracking-[0.2em] text-pink-200">Smart Flashcards</p>
                    <p class="text-white font-semibold mt-2">{{ $material->flashcardDeck ? $material->flashcardDeck->card_count . ' kartu siap dipakai' : 'Belum dibuat' }}</p>
                    <p class="text-sm text-pink-100/70 mt-1">Buka dari sidebar atau klik kartu ini untuk generate dari materi ini.</p>
                </a>
                <a href="{{ route('feature.quiz', ['material_id' => $material->id]) }}" class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 hover:bg-emerald-500/15 transition">
                    <p class="text-xs uppercase tracking-[0.2em] text-emerald-200">Latihan Kuis</p>
                    <p class="text-white font-semibold mt-2">{{ $material->quizSet ? $material->quizSet->question_count . ' soal siap dipakai' : 'Belum dibuat' }}</p>
                    <p class="text-sm text-emerald-100/70 mt-1">Buka dari sidebar atau klik kartu ini untuk mulai generate soal.</p>
                </a>
            </div>
            <div class="mt-6">
                <h3 class="font-outfit text-lg font-semibold text-white mb-3">Teks Materi</h3>
                <div class="rounded-2xl bg-gray-900/70 border border-white/5 p-4 text-sm text-gray-300 whitespace-pre-line break-words max-h-[28rem] overflow-y-auto">{{ $material->raw_text ?: 'Belum ada teks materi.' }}</div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <section class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-5 border-b border-white/5 flex items-center justify-between gap-3">
                    <h3 class="font-outfit text-lg font-semibold text-white">Ringkasan</h3>
                    <a href="{{ route('feature.summary') }}" class="text-sm text-purple-400">Semua ringkasan</a>
                </div>
                <div class="divide-y divide-white/5">
                    @forelse ($material->summaries as $summary)
                        <a href="{{ route('summaries.show', $summary) }}" class="block p-4 hover:bg-white/5 transition">
                            <p class="text-white font-medium">{{ $summary->title }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ \Illuminate\Support\Str::limit($summary->summary_text, 110) }}</p>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-gray-400">Belum ada ringkasan untuk materi ini.</div>
                    @endforelse
                </div>
            </section>

            <section class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-5 border-b border-white/5 flex items-center justify-between gap-3">
                    <h3 class="font-outfit text-lg font-semibold text-white">Thread Chat</h3>
                    <a href="{{ route('feature.chat') }}" class="text-sm text-purple-400">Buka chat</a>
                </div>
                <div class="divide-y divide-white/5">
                    @forelse ($material->chatThreads as $thread)
                        <a href="{{ route('chat.show', $thread) }}" class="block p-4 hover:bg-white/5 transition">
                            <p class="text-white font-medium">{{ $thread->title }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ $thread->messages->count() }} pesan</p>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-gray-400">Belum ada thread untuk materi ini.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
