<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Smart Flashcards</h2>
            <p class="text-sm text-gray-400 mt-1">Pilih materi dari hasil unggahan, lalu sistem akan membuat deck belajar yang bisa langsung direview.</p>
        </div>
    </x-slot>

    <style>
        .flashcard-perspective { perspective: 1200px; }
        .flashcard-stack { transform-style: preserve-3d; }
        .flashcard-face { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
        .flashcard-rotated { transform: rotateY(180deg); }
    </style>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
        @endif

        <section class="glass-panel rounded-3xl border border-white/5 p-6 md:p-8">
            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                <div class="flex-1">
                    <p class="text-xs uppercase tracking-[0.25em] text-pink-300">Sumber Materi</p>
                    <h3 class="font-outfit text-xl text-white font-semibold mt-2">Gunakan materi yang sudah diunggah</h3>
                    <p class="text-sm text-gray-400 mt-2">Deck disimpan per materi, jadi saat dibuka lagi kamu tidak perlu generate dari nol.</p>
                </div>

                <form method="GET" action="{{ route('feature.flashcards') }}" class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <select name="material_id" class="min-w-[260px] rounded-2xl border border-white/10 bg-gray-950 px-4 py-3 text-sm text-white">
                        <option value="">Pilih materi</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}" @selected($selectedMaterial?->id === $material->id)>{{ $material->title }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-2xl bg-white/10 px-5 py-3 text-sm font-medium text-white hover:bg-white/15 transition">Buka Materi</button>
                </form>
            </div>
        </section>

        @if (! $selectedMaterial)
            <section class="glass-panel rounded-3xl border border-dashed border-white/10 p-10 text-center">
                <p class="text-lg font-outfit text-white">Belum ada materi yang dipilih</p>
                <p class="text-sm text-gray-400 mt-2">Pilih satu materi untuk membuat flashcards otomatis dari teks yang sudah kamu unggah.</p>
            </section>
        @elseif (! $deck)
            <section class="glass-panel rounded-3xl border border-white/5 p-8">
                <p class="text-xs uppercase tracking-[0.25em] text-pink-300">Materi Terpilih</p>
                <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $selectedMaterial->title }}</h3>
                <p class="text-gray-400 mt-3">Materi ini belum punya deck. Generator akan mengambil poin penting dari teks materi dan membuat kartu istilah, definisi, dan konteks singkat.</p>
                <form method="POST" action="{{ route('flashcards.generate') }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                    <button type="submit" class="rounded-2xl bg-pink-600 px-6 py-3 text-sm font-semibold text-white hover:bg-pink-500 transition">Buat Flashcards</button>
                </form>
            </section>
        @else
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)] gap-6 items-start">
                <section class="glass-panel rounded-[2rem] border border-white/5 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.25em] text-pink-300">Deck Aktif</p>
                            <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $deck->title }}</h3>
                            <p class="text-sm text-gray-400 mt-2">{{ $deck->description }}</p>
                        </div>

                        <form method="POST" action="{{ route('flashcards.generate') }}">
                            @csrf
                            <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                            <button type="submit" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white hover:bg-white/10 transition">Generate Ulang</button>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-wider text-gray-400">Total Kartu</p>
                            <p class="mt-3 text-2xl font-outfit font-bold text-white">{{ $deck->card_count }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-wider text-gray-400">Siap Direview</p>
                            <p class="mt-3 text-2xl font-outfit font-bold text-white">{{ $dueCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-wider text-gray-400">Materi</p>
                            <p class="mt-3 text-sm font-semibold text-white">{{ $selectedMaterial->title }}</p>
                        </div>
                    </div>

                    @if ($currentCard)
                        @php($isDue = $currentCard->next_review_at === null || $currentCard->next_review_at->isPast())
                        <div
                            x-data="{ flipped: false }"
                            class="flashcard-perspective"
                        >
                            <div
                                class="flashcard-stack relative min-h-[24rem] cursor-pointer transition-transform duration-700"
                                :class="flipped ? 'flashcard-rotated' : ''"
                                @click="flipped = !flipped"
                            >
                                <div class="flashcard-face absolute inset-0 rounded-[2rem] border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-pink-950/60 p-8 shadow-2xl">
                                    <div class="flex items-center justify-between">
                                        <span class="rounded-full border border-pink-400/30 bg-pink-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-pink-200">Front</span>
                                        <span class="text-xs text-gray-400">Ketuk kartu untuk membalik</span>
                                    </div>
                                    <div class="flex h-full flex-col justify-center pb-6">
                                        <p class="text-sm uppercase tracking-[0.3em] text-pink-300">{{ $currentCard->difficulty }}</p>
                                        <h4 class="mt-5 text-center font-outfit text-3xl font-bold text-white md:text-5xl">{{ $currentCard->front }}</h4>
                                    </div>
                                </div>

                                <div class="flashcard-face flashcard-rotated absolute inset-0 rounded-[2rem] border border-pink-400/20 bg-gradient-to-br from-pink-600/90 via-fuchsia-700/90 to-indigo-900 p-8 shadow-[0_0_35px_rgba(236,72,153,0.2)]">
                                    <div class="flex items-center justify-between">
                                        <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Back</span>
                                        <span class="text-xs text-pink-100/70">{{ $isDue ? 'Kartu siap dinilai' : 'Belum jatuh tempo, tapi tetap bisa dibaca' }}</span>
                                    </div>
                                    <div class="flex h-full flex-col justify-center">
                                        <p class="text-xl md:text-2xl font-semibold text-white leading-relaxed">{{ $currentCard->back }}</p>
                                        @if ($currentCard->example)
                                            <p class="mt-6 text-sm leading-7 text-pink-100/85">{{ $currentCard->example }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($isDue)
                            <form method="POST" action="{{ route('flashcards.review', $deck) }}" class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-6">
                                @csrf
                                <input type="hidden" name="flashcard_id" value="{{ $currentCard->id }}">
                                <button type="submit" name="rating" value="again" class="rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-200 hover:bg-red-500/20 transition">Lagi</button>
                                <button type="submit" name="rating" value="hard" class="rounded-2xl border border-orange-500/30 bg-orange-500/10 px-4 py-3 text-sm font-semibold text-orange-200 hover:bg-orange-500/20 transition">Sulit</button>
                                <button type="submit" name="rating" value="good" class="rounded-2xl border border-blue-500/30 bg-blue-500/10 px-4 py-3 text-sm font-semibold text-blue-200 hover:bg-blue-500/20 transition">Baik</button>
                                <button type="submit" name="rating" value="easy" class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-200 hover:bg-emerald-500/20 transition">Mudah</button>
                            </form>
                        @else
                            <div class="mt-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                                Semua kartu sedang aman. Kartu berikutnya tersedia sekitar {{ optional($currentCard->next_review_at)->diffForHumans() ?? 'nanti' }}.
                            </div>
                        @endif
                    @endif
                </section>

                <aside class="glass-panel rounded-[2rem] border border-white/5 p-6">
                    <p class="text-xs uppercase tracking-[0.25em] text-pink-300">Daftar Kartu</p>
                    <div class="mt-4 space-y-3 max-h-[42rem] overflow-y-auto pr-1">
                        @foreach ($deck->cards as $card)
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-white">{{ $card->front }}</p>
                                        <p class="mt-2 text-sm text-gray-400">{{ \Illuminate\Support\Str::limit($card->back, 110) }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $card->next_review_at === null || $card->next_review_at->isPast() ? 'bg-pink-500/20 text-pink-200' : 'bg-white/10 text-gray-300' }}">
                                        {{ $card->next_review_at === null || $card->next_review_at->isPast() ? 'Due' : 'Scheduled' }}
                                    </span>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $card->difficulty }}</span>
                                    <span>{{ $card->next_review_at ? $card->next_review_at->diffForHumans() : 'Siap sekarang' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
            </div>
        @endif
    </div>
</x-app-layout>
