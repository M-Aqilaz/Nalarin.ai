<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center border border-green-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="user-kicker text-[11px] text-emerald-100/90">Quiz Arena</p>
                <h2 class="mt-2 font-outfit font-bold text-2xl leading-tight soft-gradient-text">
                    {{ $quiz ? $quiz->title : 'Latihan Kuis' }}
                </h2>
                <p class="mt-2 text-sm text-slate-300/80">
                    @if ($currentQuestion && $quiz)
                        Soal {{ (int) ($attempt['current_index'] ?? 0) + 1 }} dari {{ $quiz->questions->count() }}
                    @elseif ($quiz)
                        {{ $quiz->question_count }} soal siap dimainkan
                    @else
                        Bangun soal pilihan ganda dari materi unggahan, lalu kerjakan langsung dari halaman ini.
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    @if ($currentQuestion && $quiz)
        <x-slot name="headerActions">
            <form method="POST" action="{{ route('quiz.reset', $quiz) }}">
                @csrf
                <button type="submit" class="px-5 py-2.5 rounded-xl border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 text-red-400 font-medium text-sm transition">
                    Akhiri Kuis
                </button>
            </form>
        </x-slot>
    @endif

    <div class="readable-study-page space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
        @endif

        @php
            $nalaMood = 'flat';
            $nalaTitle = 'Pilih materi dulu';
            $nalaMessage = 'Nala belum bisa bikin kuis kalau kamu belum memilih materi. Pilih satu materi, baru Nala susun soal yang masuk akal.';

            if ($currentQuestion && $quiz) {
                $nalaMood = 'angry';
                $nalaTitle = 'Fokus, ini sedang kuis';
                $nalaMessage = 'Baca soal pelan-pelan sebelum memilih jawaban. Jangan asal klik cuma karena mau cepat selesai.';
            } elseif ($results) {
                $nalaMood = $results['score'] >= ceil($results['total'] * 0.7) ? 'happy' : 'sad';
                $nalaTitle = $results['score'] >= ceil($results['total'] * 0.7) ? 'Lumayan, kamu paham' : 'Belum aman, ulangi lagi';
                $nalaMessage = 'Cek pembahasan soal yang salah, lalu ulangi kuis setelah membaca ringkasan. Nala tidak marah, cuma sedikit kecewa kalau kamu menyerah.';
            } elseif ($quiz) {
                $nalaMood = 'happy';
                $nalaTitle = 'Kuis sudah siap';
                $nalaMessage = 'Mulai kuis saat kamu siap. Jawab dengan tenang, bukan dengan nebak-nebak panik.';
            } elseif ($selectedMaterial) {
                $nalaMood = 'flat';
                $nalaTitle = 'Materi sudah dipilih';
                $nalaMessage = 'Sekarang buat kuis dari materi ini. Nala akan susun pilihan ganda dari konsep pentingnya.';
            }
        @endphp

        <x-nala-guide :mood="$nalaMood" :title="$nalaTitle" :message="$nalaMessage" compact />

        <section class="feature-hero">
            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                <div class="flex-1">
                    <p class="user-kicker text-[11px] text-emerald-100/90">Sumber Materi</p>
                    <h3 class="mt-2 font-outfit text-xl font-semibold text-white">Pilih materi untuk dijadikan kuis</h3>
                    <p class="mt-2 text-sm text-slate-100/80">Soal disimpan per materi, jadi kamu bisa ulangi kuis tanpa kehilangan bank soal yang sudah dibuat.</p>
                </div>

                <form method="GET" action="{{ route('feature.quiz') }}" class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto">
                    <select name="material_id" class="glass-input w-full px-4 py-3 text-sm sm:min-w-[260px]">
                        <option value="">Pilih materi</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}" @selected($selectedMaterial?->id === $material->id)>{{ $material->title }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-2xl border border-white/10 bg-white/[0.08] px-5 py-3 text-sm font-medium text-white transition hover:bg-white/[0.14]">Buka Materi</button>
                </form>
            </div>
        </section>

        @if (! $selectedMaterial)
            <section class="glass-panel rounded-3xl border border-dashed border-white/10 p-6 text-center sm:p-10">
                <p class="text-lg font-outfit text-white">Belum ada materi yang dipilih</p>
                <p class="text-sm text-gray-400 mt-2">Pilih satu materi untuk membuat latihan kuis pilihan ganda dari isi materi tersebut.</p>
            </section>
        @elseif (! $quiz)
            <section class="glass-panel accent-card-emerald rounded-3xl p-6 sm:p-8">
                <p class="user-kicker text-[11px] text-emerald-100/90">Materi Terpilih</p>
                <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $selectedMaterial->title }}</h3>
                <p class="mt-3 text-slate-200/75">AI akan menyusun soal pilihan ganda dari konsep penting, dengan 4 opsi unik dan kunci jawaban yang divalidasi.</p>
                @unless (auth()->user()->isPremium())
                    <p class="mt-3 text-xs text-slate-300/55">Akun free dibatasi {{ config('services.openai.limits.content_free_per_day', 6) }} generate AI per hari untuk flashcard dan kuis.</p>
                @endunless
                <form method="POST" action="{{ route('quiz.generate') }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                    <button type="submit" class="user-primary-button px-6 py-3 text-sm">Buat Kuis</button>
                </form>
            </section>
        @elseif ($results)
            <section class="glass-panel accent-card-emerald rounded-[2rem] p-6 md:p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">Hasil Kuis</p>
                        <h3 class="font-outfit text-3xl font-bold text-white mt-2">{{ $results['score'] }} / {{ $results['total'] }}</h3>
                        <p class="text-sm text-gray-400 mt-2">Lihat pembahasan setiap soal di bawah, lalu ulangi kuis kalau ingin menguji lagi pemahamanmu.</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <form method="POST" action="{{ route('quiz.reset', $quiz) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl border border-white/10 bg-white/[0.08] px-4 py-2.5 text-sm text-white transition hover:bg-white/[0.14]">Reset</button>
                        </form>
                        <form method="POST" action="{{ route('quiz.start', $quiz) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-400 transition">Ulangi Kuis</button>
                        </form>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    @foreach ($results['items'] as $index => $item)
                        <div class="rounded-3xl border {{ $item['is_correct'] ? 'border-emerald-500/20 bg-emerald-500/10' : 'border-rose-500/20 bg-rose-500/10' }} p-5">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $item['is_correct'] ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">{{ $index + 1 }}</div>
                                <div class="flex-1">
                                    <p class="font-semibold text-white">{{ $item['prompt'] }}</p>
                                    <p class="mt-3 text-sm text-gray-200">Jawabanmu: {{ $item['selected'] ?? 'Belum dijawab' }}</p>
                                    <p class="mt-1 text-sm text-gray-100">Jawaban benar: {{ $item['correct'] }}</p>
                                    @if ($item['explanation'])
                                        <p class="mt-3 text-sm leading-7 {{ $item['is_correct'] ? 'text-emerald-100/90' : 'text-rose-100/90' }}">{{ $item['explanation'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @elseif ($currentQuestion)
            @php($currentIndex = (int) ($attempt['current_index'] ?? 0))
            @php($totalQuestions = $quiz->questions->count())
            @php($progress = $totalQuestions > 0 ? round(($currentIndex / $totalQuestions) * 100) : 0)
            <div class="mx-auto max-w-4xl py-4">
                <div class="mb-8 h-2 w-full overflow-hidden rounded-full bg-slate-900/80">
                    <div class="h-full bg-gradient-to-r from-green-500 to-emerald-400 rounded-full relative" style="width: {{ $progress }}%">
                        <div class="absolute right-0 top-0 bottom-0 w-4 bg-white/20 blur-[2px]"></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('quiz.answer', $quiz) }}" class="glass-panel accent-card-emerald relative overflow-hidden rounded-3xl p-5 shadow-2xl sm:p-8 md:p-10">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                    <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-green-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/5 font-outfit text-xl font-bold text-gray-300">
                            {{ $currentIndex + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl md:text-2xl font-bold text-white mb-6 leading-relaxed font-outfit">{{ $currentQuestion->prompt }}</h4>

                            <div class="mt-8 space-y-3">
                                @foreach (($currentQuestion->choices ?? []) as $choiceIndex => $choice)
                                    <label class="block relative cursor-pointer group">
                                        <input type="radio" name="choice" value="{{ $choiceIndex }}" class="peer sr-only" required>
                                        <div class="flex w-full items-start gap-4 rounded-xl border-2 border-white/10 bg-white/5 p-4 font-medium text-gray-300 transition-all group-hover:border-white/20 group-hover:bg-white/10 peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-white">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-gray-800 text-sm font-bold">{{ chr(65 + $choiceIndex) }}</span>
                                            <span class="min-w-0 break-words">{{ $choice }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-6 py-2.5 text-sm font-medium text-white opacity-60 sm:justify-start">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    Sebelumnya
                                </div>
                                <button type="submit" class="flex items-center justify-center gap-2 rounded-xl bg-green-500 px-8 py-2.5 text-sm font-semibold text-white shadow-[0_0_15px_rgba(34,197,94,0.4)] transition hover:bg-green-400">
                                    {{ $currentIndex + 1 === $totalQuestions ? 'Selesai' : 'Selanjutnya' }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <section class="glass-panel accent-card-emerald rounded-[2rem] p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">Kuis Siap</p>
                        <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $quiz->title }}</h3>
                        <p class="text-sm text-gray-400 mt-2">{{ $quiz->description }}</p>
                    </div>

                    <form method="POST" action="{{ route('quiz.generate') }}" class="w-full lg:w-auto">
                        @csrf
                        <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                        <button type="submit" class="rounded-2xl border border-white/10 bg-white/[0.08] px-4 py-2.5 text-sm text-white transition hover:bg-white/[0.14]">Generate Ulang</button>
                    </form>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-8">
                    <div class="glass-panel rounded-2xl p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">Total Soal</p>
                        <p class="mt-3 text-2xl font-outfit font-bold text-white">{{ $quiz->question_count }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">Materi</p>
                        <p class="mt-3 text-sm font-semibold text-white">{{ $selectedMaterial->title }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">Format</p>
                        <p class="mt-3 text-sm font-semibold text-white">Pilihan Ganda</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <form method="POST" action="{{ route('quiz.start', $quiz) }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-400 transition">Mulai Kuis</button>
                    </form>
                    <form method="POST" action="{{ route('quiz.reset', $quiz) }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl border border-white/10 bg-white/[0.08] px-6 py-3 text-sm font-medium text-white transition hover:bg-white/[0.14]">Reset Progress</button>
                    </form>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
