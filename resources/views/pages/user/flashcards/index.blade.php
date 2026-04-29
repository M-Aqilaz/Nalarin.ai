<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center border border-pink-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                    {{ $deck ? $deck->title : 'Smart Flashcards' }}
                </h2>
                <p class="text-sm text-gray-400 mt-1">
                    @if ($deck && $currentCard)
                        Kartu {{ $currentCard->sort_order }} dari {{ $deck->card_count }} | Ketuk kartu untuk membalik
                    @else
                        Pilih materi dari hasil unggahan, lalu sistem akan membuat deck belajar yang bisa langsung direview.
                    @endif
                </p>
            </div>
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
                <p class="text-gray-400 mt-3">Materi ini belum punya deck. AI akan membuat kartu istilah/konsep yang jelas dengan definisi singkat dari teks materi.</p>
                @unless (auth()->user()->isPremium())
                    <p class="mt-3 text-xs text-gray-500">Akun free dibatasi {{ config('services.openai.limits.content_free_per_day', 6) }} generate AI per hari untuk flashcard dan kuis.</p>
                @endunless
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
                        <div class="max-w-3xl mx-auto py-6 flex flex-col items-center">
                            <div x-data="{ flipped: false }" class="w-full max-w-xl h-80 flashcard-perspective cursor-pointer" @click="flipped = !flipped">
                                <div class="flashcard-stack w-full h-full relative transition-transform duration-700 shadow-2xl" :class="flipped ? 'flashcard-rotated' : ''">
                                    <div class="flashcard-face absolute inset-0 w-full h-full glass-panel rounded-3xl border border-white/10 flex flex-col items-center justify-center p-8">
                                        <p class="absolute top-6 left-6 text-xs font-bold tracking-wider text-pink-400 uppercase">Istilah / Front</p>
                                        <svg class="absolute top-6 right-6 w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                                        <div class="text-center">
                                            <p class="text-xs uppercase tracking-[0.3em] text-pink-300">{{ $currentCard->difficulty }}</p>
                                            <h2 class="mt-5 text-4xl md:text-5xl font-bold font-outfit text-white text-center">{{ $currentCard->front }}</h2>
                                        </div>
                                    </div>

                                    <div class="flashcard-face flashcard-rotated absolute inset-0 w-full h-full bg-gradient-to-br from-pink-600 to-purple-700 rounded-3xl border border-white/10 flex flex-col items-center justify-center p-8 shadow-[0_0_30px_rgba(219,39,119,0.3)]">
                                        <p class="absolute top-6 left-6 text-xs font-bold tracking-wider text-pink-200 uppercase">Definisi / Back</p>
                                        <div class="text-center">
                                            <h3 class="text-2xl font-bold font-outfit text-white mb-4 italic">"{{ $currentCard->back }}"</h3>
                                            @if ($currentCard->example)
                                                <p class="text-pink-100/80 text-sm">{{ $currentCard->example }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 mt-10">
                                <div class="w-12 h-12 rounded-full border border-white/10 bg-white/5 text-white flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </div>
                                <span class="text-gray-400 font-medium font-outfit text-lg w-20 text-center">{{ $currentCard->sort_order }} / {{ $deck->card_count }}</span>
                                <div class="w-12 h-12 rounded-full bg-pink-600 text-white flex items-center justify-center shadow-[0_0_15px_rgba(219,39,119,0.4)]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </div>

                        @if ($isDue)
                            <form method="POST" action="{{ route('flashcards.review', $deck) }}" class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-8 max-w-lg mx-auto">
                                @csrf
                                <input type="hidden" name="flashcard_id" value="{{ $currentCard->id }}">
                                <button type="submit" name="rating" value="again" class="py-3 px-4 rounded-xl border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 text-red-200 font-semibold text-xs tracking-wide transition">Lagi</button>
                                <button type="submit" name="rating" value="hard" class="py-3 px-4 rounded-xl border border-orange-500/30 bg-orange-500/10 hover:bg-orange-500/20 text-orange-200 font-semibold text-xs tracking-wide transition">Sulit</button>
                                <button type="submit" name="rating" value="good" class="py-3 px-4 rounded-xl border border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20 text-blue-200 font-semibold text-xs tracking-wide transition">Baik</button>
                                <button type="submit" name="rating" value="easy" class="py-3 px-4 rounded-xl border border-green-500/30 bg-green-500/10 hover:bg-green-500/20 text-green-200 font-semibold text-xs tracking-wide transition">Mudah</button>
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
