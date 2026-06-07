<x-app-layout>
    @php
        $user = auth()->user();
        $shortcuts = [
            ['label' => 'Unggah Materi', 'desc' => 'Masukkan PDF, gambar, atau teks baru.', 'href' => route('feature.upload'), 'tone' => 'from-sky-100 to-white', 'icon' => 'UP'],
            ['label' => 'Ringkasan', 'desc' => 'Baca ulang hasil rangkuman AI.', 'href' => route('feature.summary'), 'tone' => 'from-rose-100 to-pink-50', 'icon' => 'AI'],
            ['label' => 'Tutor AI', 'desc' => 'Tanya Nala dari thread chat.', 'href' => route('feature.chat'), 'tone' => 'from-violet-100 to-fuchsia-50', 'icon' => 'N'],
            ['label' => 'Flashcards', 'desc' => 'Latihan hafalan dari materi.', 'href' => route('feature.flashcards'), 'tone' => 'from-cyan-100 to-teal-50', 'icon' => 'FC'],
            ['label' => 'Kuis', 'desc' => 'Uji pemahaman dengan soal.', 'href' => route('feature.quiz'), 'tone' => 'from-amber-100 to-yellow-50', 'icon' => 'QZ'],
            ['label' => 'Study Matching', 'desc' => 'Cari partner belajar aktif.', 'href' => route('matchmaking.index'), 'tone' => 'from-emerald-100 to-cyan-50', 'icon' => 'SM'],
        ];
    @endphp

    <x-slot name="header">
        <div>
            <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-slate-800">Learning Hub</p>
            <h2 class="mt-2 font-outfit text-3xl font-extrabold leading-tight text-slate-950 md:text-4xl">Dashboard Belajar</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-800">Mulai dari Nala, lanjut ke materi, latihan, fokus, dan partner belajar dari satu tempat.</p>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('feature.upload') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-500 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600 md:w-auto">Materi Baru</a>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-[1.75rem] border border-sky-200 bg-white/88 p-5 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
            <div class="grid gap-5 lg:grid-cols-[1fr_220px] lg:items-center">
                <div>
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Nala Guide</p>
                    <h3 class="mt-2 font-outfit text-2xl font-extrabold text-slate-950">Mau mulai dari mana?</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Semua fitur utama Nalarin.ai ada di launcher ini. Pilih alur belajar yang paling relevan hari ini.</p>
                </div>
                <div class="hidden justify-center lg:flex">
                    <img src="{{ asset('images/NALA.png') }}" class="h-36 w-auto object-contain drop-shadow-[0_18px_28px_rgba(14,116,144,0.18)]" alt="Nala">
                </div>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                @foreach ($shortcuts as $shortcut)
                    <a href="{{ $shortcut['href'] }}" class="group min-h-[126px] rounded-[1.4rem] border border-sky-200 bg-gradient-to-br {{ $shortcut['tone'] }} p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-[0_18px_35px_rgba(14,116,144,0.14)]">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/80 text-sm font-extrabold text-sky-700 shadow-sm">{{ $shortcut['icon'] }}</span>
                        <h4 class="mt-4 font-outfit text-lg font-extrabold text-slate-950">{{ $shortcut['label'] }}</h4>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $shortcut['desc'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => 'Materi', 'value' => $materialCount, 'tone' => 'from-sky-100 to-white', 'icon' => 'MT'],
                ['label' => 'Ringkasan', 'value' => $summaryCount, 'tone' => 'from-rose-100 to-pink-50', 'icon' => 'AI'],
                ['label' => 'Thread AI', 'value' => $threadCount, 'tone' => 'from-violet-100 to-fuchsia-50', 'icon' => 'N'],
                ['label' => 'Room Kelas', 'value' => $roomCount, 'tone' => 'from-cyan-100 to-teal-50', 'icon' => 'RM'],
                ['label' => 'Match Aktif', 'value' => $activeMatchCount, 'tone' => 'from-amber-100 to-yellow-50', 'icon' => 'SM'],
            ] as $stat)
                <article class="min-h-[112px] rounded-[1.65rem] border border-sky-200 bg-gradient-to-br {{ $stat['tone'] }} p-5 shadow-[0_18px_35px_rgba(14,116,144,0.12)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-700">{{ $stat['label'] }}</p>
                            <p class="mt-4 font-roboto text-3xl font-extrabold text-slate-950">{{ $stat['value'] }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/75 text-sm font-extrabold text-sky-700 shadow-sm">{{ $stat['icon'] }}</div>
                    </div>
                </article>
            @endforeach
        </div>

        <section class="rounded-[1.75rem] border border-sky-300 bg-gradient-to-r from-sky-500 to-teal-400 p-5 text-white shadow-[0_20px_45px_rgba(14,165,233,0.25)] md:flex md:items-center md:justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-white/85">Plan {{ $user->plan }}</p>
                <p class="mt-4 text-xl font-extrabold">Sisa kuota study matching: {{ $user->match_credits }}</p>
                <p class="mt-2 text-sm text-white/90">Upgrade premium untuk room lebih banyak, match tanpa batas, dan fitur sosial penuh.</p>
            </div>
            <a href="{{ route('pricing') }}" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-sky-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-900/20 transition hover:bg-sky-700 md:mt-0 md:w-auto">Lihat Pricing</a>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/88 shadow-[0_18px_35px_rgba(14,116,144,0.12)] backdrop-blur">
                <div class="flex items-center justify-between border-b border-sky-200 p-5">
                    <h3 class="font-outfit text-xl font-extrabold text-slate-950">Room Kelas</h3>
                    <a href="{{ route('rooms.index') }}" class="text-sm font-semibold text-cyan-700">Buka room</a>
                </div>
                <div class="min-h-[210px] p-4">
                    @forelse ($recentRooms as $room)
                        <a href="{{ route('rooms.show', $room) }}" class="block rounded-xl bg-sky-100 px-4 py-3 transition hover:bg-sky-200">
                            <p class="font-bold text-slate-950">{{ $room->name }}</p>
                            <p class="mt-1 text-sm text-slate-700">{{ $room->topic }} | {{ $room->members_count }} member</p>
                        </a>
                    @empty
                        <div class="text-sm text-slate-700">Belum ikut room.</div>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/88 shadow-[0_18px_35px_rgba(14,116,144,0.12)] backdrop-blur">
                <div class="flex items-center justify-between border-b border-sky-200 p-5">
                    <h3 class="font-outfit text-xl font-extrabold text-slate-950">Materi Terbaru</h3>
                    <a href="{{ route('materials.index') }}" class="text-sm font-semibold text-cyan-700">Lihat semua</a>
                </div>
                <div class="divide-y divide-sky-100">
                    @forelse ($recentMaterials as $material)
                        <a href="{{ route('materials.show', $material) }}" class="flex items-center gap-4 p-4 transition hover:bg-sky-50">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700">AI</span>
                            <span>
                                <span class="block font-bold text-slate-950">{{ $material->title }}</span>
                                <span class="mt-1 block text-sm text-slate-700">{{ $material->status }} | {{ $material->summaries->count() }} ringkasan</span>
                            </span>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-slate-700">Belum ada materi.</div>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/88 shadow-[0_18px_35px_rgba(14,116,144,0.12)] backdrop-blur">
                <div class="flex items-center justify-between border-b border-sky-200 p-5">
                    <h3 class="font-outfit text-xl font-extrabold text-slate-950">Thread AI</h3>
                    <a href="{{ route('feature.chat') }}" class="text-sm font-semibold text-cyan-700">Buka chat</a>
                </div>
                <div class="min-h-[210px] p-4">
                    @forelse ($recentThreads as $thread)
                        <a href="{{ route('chat.show', $thread) }}" class="block rounded-xl px-1 py-2 transition hover:bg-sky-50">
                            <p class="font-bold text-slate-950">{{ $thread->title }}</p>
                            <p class="mt-1 text-sm text-slate-700">{{ $thread->messages_count }} pesan | {{ $thread->material?->title ?? 'Tanpa materi' }}</p>
                        </a>
                    @empty
                        <div class="text-sm text-slate-700">Belum ada thread chat.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
