<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Latihan Kuis</h2>
            <p class="text-sm text-gray-400 mt-1">Bangun soal pilihan ganda dari materi unggahan, lalu kerjakan langsung dari halaman ini.</p>
        </div>
    </x-slot>

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
                    <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">Sumber Materi</p>
                    <h3 class="font-outfit text-xl text-white font-semibold mt-2">Pilih materi untuk dijadikan kuis</h3>
                    <p class="text-sm text-gray-400 mt-2">Soal disimpan per materi, jadi kamu bisa ulangi kuis tanpa kehilangan bank soal yang sudah dibuat.</p>
                </div>

                <form method="GET" action="{{ route('feature.quiz') }}" class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
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
                <p class="text-sm text-gray-400 mt-2">Pilih satu materi untuk membuat latihan kuis pilihan ganda dari isi materi tersebut.</p>
            </section>
        @elseif (! $quiz)
            <section class="glass-panel rounded-3xl border border-white/5 p-8">
                <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">Materi Terpilih</p>
                <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $selectedMaterial->title }}</h3>
                <p class="text-gray-400 mt-3">Generator akan menyusun soal pilihan ganda dari konsep penting yang ditemukan pada materi ini.</p>
                <form method="POST" action="{{ route('quiz.generate') }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                    <button type="submit" class="rounded-2xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-400 transition">Buat Kuis</button>
                </form>
            </section>
        @elseif ($results)
            <section class="glass-panel rounded-[2rem] border border-white/5 p-6 md:p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">Hasil Kuis</p>
                        <h3 class="font-outfit text-3xl font-bold text-white mt-2">{{ $results['score'] }} / {{ $results['total'] }}</h3>
                        <p class="text-sm text-gray-400 mt-2">Lihat pembahasan setiap soal di bawah, lalu ulangi kuis kalau ingin menguji lagi pemahamanmu.</p>
                    </div>

                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('quiz.reset', $quiz) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white hover:bg-white/10 transition">Reset</button>
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
            <section class="glass-panel rounded-[2rem] border border-white/5 p-6 md:p-8">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">Kuis Aktif</p>
                        <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $quiz->title }}</h3>
                    </div>
                    <form method="POST" action="{{ route('quiz.reset', $quiz) }}">
                        @csrf
                        <button type="submit" class="rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-2.5 text-sm text-red-200 hover:bg-red-500/15 transition">Akhiri Kuis</button>
                    </form>
                </div>

                <div class="h-2 w-full overflow-hidden rounded-full bg-white/5 mb-8">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: {{ $progress }}%"></div>
                </div>

                <form method="POST" action="{{ route('quiz.answer', $quiz) }}" class="rounded-[2rem] border border-white/10 bg-white/5 p-6 md:p-8">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">

                    <div class="flex gap-4">
                        <div class="mt-1 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-gray-950 text-lg font-bold text-gray-300">
                            {{ $currentIndex + 1 }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-400">Soal {{ $currentIndex + 1 }} dari {{ $totalQuestions }}</p>
                            <h4 class="mt-3 text-xl font-semibold leading-relaxed text-white">{{ $currentQuestion->prompt }}</h4>

                            <div class="mt-8 space-y-3">
                                @foreach (($currentQuestion->choices ?? []) as $choiceIndex => $choice)
                                    <label class="block cursor-pointer">
                                        <input type="radio" name="choice" value="{{ $choiceIndex }}" class="peer sr-only" required>
                                        <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-gray-950/60 px-4 py-4 text-gray-300 transition peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 peer-checked:text-white hover:border-white/20 hover:bg-white/5">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-sm font-bold">{{ chr(65 + $choiceIndex) }}</span>
                                            <span>{{ $choice }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" class="rounded-2xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-400 transition">
                                    {{ $currentIndex + 1 === $totalQuestions ? 'Selesai' : 'Lanjut' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        @else
            <section class="glass-panel rounded-[2rem] border border-white/5 p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">Kuis Siap</p>
                        <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $quiz->title }}</h3>
                        <p class="text-sm text-gray-400 mt-2">{{ $quiz->description }}</p>
                    </div>

                    <form method="POST" action="{{ route('quiz.generate') }}">
                        @csrf
                        <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                        <button type="submit" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white hover:bg-white/10 transition">Generate Ulang</button>
                    </form>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-8">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">Total Soal</p>
                        <p class="mt-3 text-2xl font-outfit font-bold text-white">{{ $quiz->question_count }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">Materi</p>
                        <p class="mt-3 text-sm font-semibold text-white">{{ $selectedMaterial->title }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">Format</p>
                        <p class="mt-3 text-sm font-semibold text-white">Pilihan Ganda</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <form method="POST" action="{{ route('quiz.start', $quiz) }}">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-400 transition">Mulai Kuis</button>
                    </form>
                    <form method="POST" action="{{ route('quiz.reset', $quiz) }}">
                        @csrf
                        <button type="submit" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-sm font-medium text-white hover:bg-white/10 transition">Reset Progress</button>
                    </form>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
