<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-fuchsia-100/90">Study Roulette</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">Partner Belajar Acak</h2>
            <p class="mt-2 text-sm text-slate-300/80">Masuk, temukan partner, ngobrol singkat, lalu lanjut atau ganti partner. Mode cepat seperti OmeTV, tapi tetap fokus belajar.</p>
        </div>
    </x-slot>

    @php
        $partner = $activeMatch?->partnerFor(auth()->user());
        $profile = $user->studyProfile;
        $partnerProfile = $partner?->studyProfile;
        $isSearching = ! $activeMatch && (bool) $queue;
    @endphp

    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
        @endif

        @if ($activeMatch)
            <div
                x-data="matchChat({
                    initialMessages: {{ \Illuminate\Support\Js::from(
                        $activeMatch->messages
                            ->map(fn ($message) => \App\Support\RealtimePayloads::matchMessage($message))
                            ->values()
                    ) }},
                    sendUrl: '{{ route('matches.messages.store', $activeMatch) }}',
                    pollUrl: '{{ route('matches.messages.index', $activeMatch) }}',
                    typingUrl: '{{ route('matches.typing', $activeMatch) }}',
                    channelName: 'match.{{ $activeMatch->id }}',
                    currentUserId: {{ auth()->id() }},
                    currentUserName: {{ \Illuminate\Support\Js::from(auth()->user()->name) }},
                })"
                class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]"
            >
                <section class="overflow-hidden rounded-[2rem] border border-sky-200 bg-white/90 shadow-[0_24px_70px_rgba(14,116,144,0.16)]">
                    <div class="border-b border-sky-100 bg-sky-50/90 px-4 py-3 sm:px-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.2em] text-emerald-700">Connected</span>
                                    <span class="rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600" x-text="connectionState"></span>
                                </div>
                                <h3 class="mt-2 font-outfit text-xl font-extrabold text-slate-950">Sesi belajar dengan {{ $partner?->name ?? 'Partner' }}</h3>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('matchmaking.roulette.next') }}">
                                    @csrf
                                    <button class="inline-flex h-11 items-center justify-center rounded-2xl bg-sky-500 px-5 text-sm font-extrabold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-600">Next</button>
                                </form>
                                <form method="POST" action="{{ route('matchmaking.roulette.stop') }}">
                                    @csrf
                                    <button class="inline-flex h-11 items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-5 text-sm font-extrabold text-red-700 transition hover:bg-red-100">Stop</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 bg-gradient-to-br from-sky-50 via-white to-cyan-50 p-3 md:grid-cols-2 md:p-5">
                        <article class="relative min-h-[320px] overflow-hidden rounded-[1.75rem] border border-sky-100 bg-gradient-to-br from-white to-sky-100 p-5 shadow-sm">
                            <div class="absolute right-5 top-5 rounded-full bg-white/80 px-3 py-1 text-xs font-bold text-slate-600">Kamu</div>
                            <div class="flex h-full flex-col items-center justify-center text-center">
                                <div class="flex h-28 w-28 items-center justify-center rounded-full bg-slate-950 text-4xl font-extrabold text-white shadow-xl shadow-slate-950/10">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <h4 class="mt-5 font-outfit text-2xl font-extrabold text-slate-950">{{ auth()->user()->name }}</h4>
                                <p class="mt-2 text-sm text-slate-600">{{ $profile?->primary_subject ?? 'Subjek belum diisi' }}</p>
                                <div class="mt-5 grid w-full max-w-sm gap-2 text-left">
                                    <div class="rounded-2xl border border-sky-100 bg-white/80 p-3">
                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Target</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $profile?->goal ?? 'Belum ada target' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-sky-100 bg-white/80 p-3">
                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Gaya</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $profile?->study_style ?? 'Belum diisi' }}</p>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <article class="relative min-h-[320px] overflow-hidden rounded-[1.75rem] border border-cyan-100 bg-gradient-to-br from-cyan-50 to-white p-5 shadow-sm">
                            <div class="absolute right-5 top-5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Partner</div>
                            <div class="flex h-full flex-col items-center justify-center text-center">
                                <div class="flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-cyan-400 text-4xl font-extrabold text-white shadow-xl shadow-sky-500/20">
                                    {{ strtoupper(substr($partner?->name ?? 'P', 0, 1)) }}
                                </div>
                                <h4 class="mt-5 font-outfit text-2xl font-extrabold text-slate-950">{{ $partner?->name ?? 'Partner tidak tersedia' }}</h4>
                                <p class="mt-2 text-sm text-slate-600">{{ $partnerProfile?->primary_subject ?? 'Subjek belum diisi' }}</p>
                                <div class="mt-5 grid w-full max-w-sm gap-2 text-left">
                                    <div class="rounded-2xl border border-cyan-100 bg-white/80 p-3">
                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Target</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $partnerProfile?->goal ?? 'Belum ada target' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-cyan-100 bg-white/80 p-3">
                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Bio</p>
                                        <p class="mt-1 line-clamp-3 text-sm font-semibold leading-6 text-slate-800">{{ $partnerProfile?->bio ?? 'Belum ada bio' }}</p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="border-t border-sky-100 bg-white/95 p-4 sm:p-5">
                        <div class="grid gap-3 md:grid-cols-4">
                            <form method="POST" action="{{ route('matchmaking.roulette.next') }}">
                                @csrf
                                <button class="inline-flex h-12 w-full items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 text-sm font-extrabold text-sky-700 transition hover:bg-sky-100">Partner Baru</button>
                            </form>
                            <form method="POST" action="{{ route('matchmaking.roulette.stop') }}">
                                @csrf
                                <button class="inline-flex h-12 w-full items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-4 text-sm font-extrabold text-red-700 transition hover:bg-red-100">Akhiri</button>
                            </form>
                            <details class="md:col-span-1">
                                <summary class="inline-flex h-12 w-full cursor-pointer list-none items-center justify-center rounded-2xl border border-amber-200 bg-amber-50 px-4 text-sm font-extrabold text-amber-700 transition hover:bg-amber-100">Report</summary>
                                <form method="POST" action="{{ route('matches.report', $activeMatch) }}" class="mt-3 rounded-2xl border border-amber-100 bg-white p-3">
                                    @csrf
                                    <textarea name="reason" rows="3" class="w-full rounded-xl border border-amber-200 px-3 py-2 text-sm text-slate-950 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100" placeholder="Alasan laporan" required></textarea>
                                    <button class="mt-2 w-full rounded-xl bg-amber-500 px-3 py-2 text-sm font-extrabold text-white">Kirim Report</button>
                                </form>
                            </details>
                            <form method="POST" action="{{ route('matches.block', $activeMatch) }}" onsubmit="return confirm('Blokir partner ini dan tutup sesi?')">
                                @csrf
                                <button class="inline-flex h-12 w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-700 transition hover:bg-slate-50">Block</button>
                            </form>
                        </div>
                    </div>
                </section>

                <aside class="flex min-h-[calc(100vh-13rem)] flex-col overflow-hidden rounded-[2rem] border border-sky-200 bg-white/90 shadow-[0_24px_70px_rgba(14,116,144,0.12)]">
                    <div class="border-b border-sky-100 bg-sky-50/90 px-5 py-4">
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Live Chat</p>
                        <h3 class="mt-1 font-outfit text-xl font-extrabold text-slate-950">{{ $partner?->name ?? 'Partner' }}</h3>
                    </div>

                    <div x-ref="messageList" class="min-h-0 flex-1 space-y-3 overflow-y-auto bg-gradient-to-b from-white to-sky-50/70 p-4">
                        <div x-show="!booted" class="space-y-3">
                            @forelse ($activeMatch->messages as $message)
                                <div class="{{ $message->user_id === auth()->id() ? 'ml-auto bg-slate-950 text-white' : 'mr-auto border border-sky-100 bg-white text-slate-900' }} max-w-[88%] rounded-2xl px-4 py-3 shadow-sm">
                                    <p class="{{ $message->user_id === auth()->id() ? 'text-white/60' : 'text-slate-400' }} mb-1 text-[11px] font-extrabold uppercase tracking-[0.16em]">{{ $message->user->name }}</p>
                                    <p class="whitespace-pre-line break-words text-sm leading-6">{{ $message->content }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-sky-200 bg-white p-4 text-sm leading-6 text-slate-600">Mulai dengan sapaan singkat dan target belajarmu hari ini.</div>
                            @endforelse
                        </div>

                        <div x-cloak x-show="booted" class="space-y-3">
                            <template x-if="messages.length === 0">
                                <div class="rounded-2xl border border-dashed border-sky-200 bg-white p-4 text-sm leading-6 text-slate-600">Mulai dengan sapaan singkat dan target belajarmu hari ini.</div>
                            </template>

                            <template x-for="message in messages" :key="message.id">
                                <div :class="Number(message.user_id) === Number(currentUserId) ? 'ml-auto bg-slate-950 text-white' : 'mr-auto border border-sky-100 bg-white text-slate-900'" class="max-w-[88%] rounded-2xl px-4 py-3 shadow-sm">
                                    <p :class="Number(message.user_id) === Number(currentUserId) ? 'text-white/60' : 'text-slate-400'" class="mb-1 text-[11px] font-extrabold uppercase tracking-[0.16em]" x-text="message.user_name"></p>
                                    <p class="whitespace-pre-line break-words text-sm leading-6" x-text="message.content"></p>
                                </div>
                            </template>

                            <div x-cloak x-show="typingText" class="mr-auto max-w-[88%] rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm text-slate-600 shadow-sm">
                                <div class="flex items-center gap-2">
                                    <span x-text="typingText"></span>
                                    <span class="typing-dots" aria-hidden="true"><span></span><span></span><span></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('matches.messages.store', $activeMatch) }}" method="POST" class="border-t border-sky-100 bg-white p-4" @submit.prevent="submitMessage">
                        @csrf
                        <textarea x-model="form.content" @input="notifyTyping" name="content" rows="3" class="min-h-[92px] w-full resize-none rounded-2xl border border-sky-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100" placeholder="Ketik pesan..." required></textarea>
                        <div x-cloak x-show="error" class="mt-3 rounded-2xl border border-red-500/30 bg-red-500/10 p-3 text-sm text-red-700" x-text="error"></div>
                        <button class="mt-3 inline-flex h-11 w-full items-center justify-center rounded-2xl bg-sky-500 px-5 text-sm font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600 disabled:opacity-60" :disabled="isSubmitting">
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim'"></span>
                        </button>
                    </form>
                </aside>
            </div>
        @else
            <section class="overflow-hidden rounded-[2rem] border border-sky-200 bg-white/90 shadow-[0_24px_70px_rgba(14,116,144,0.16)]">
                <div class="grid min-h-[calc(100vh-14rem)] lg:grid-cols-[minmax(0,1fr)_340px]">
                    <main class="relative flex items-center justify-center overflow-hidden bg-gradient-to-br from-sky-50 via-white to-cyan-100 p-5">
                        <div class="absolute left-8 top-8 rounded-full bg-white/80 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.2em] text-sky-700">
                            {{ $isSearching ? 'Searching' : 'Idle' }}
                        </div>

                        <div class="mx-auto max-w-2xl text-center">
                            <div class="mx-auto flex h-48 w-48 items-center justify-center rounded-[2.25rem] border border-sky-200 bg-white/85 shadow-[0_24px_50px_rgba(14,116,144,0.14)]">
                                <img src="{{ asset('images/nalaFaces/nala_mentahan-happy.png') }}" class="h-40 w-40 object-contain" alt="Nala">
                            </div>

                            <h3 class="mt-8 font-outfit text-4xl font-extrabold leading-tight text-slate-950 md:text-5xl">
                                {{ $isSearching ? 'Nala sedang mencari partner...' : 'Siap cari partner belajar?' }}
                            </h3>
                            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-slate-700">
                                {{ $isSearching ? 'Tetap di halaman ini. Kalau ada user lain yang cocok, sesi belajar akan muncul di sini.' : 'Tekan mulai untuk masuk antrean cepat. Tidak perlu topik panjang, profil belajarmu akan dipakai sebagai konteks.' }}
                            </p>

                            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                                @if ($isSearching)
                                    <form method="POST" action="{{ route('matchmaking.roulette.stop') }}">
                                        @csrf
                                        <button class="inline-flex h-12 w-full items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-6 text-sm font-extrabold text-red-700 transition hover:bg-red-100 sm:w-auto">Stop Search</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('matchmaking.roulette.start') }}">
                                        @csrf
                                        <button class="inline-flex h-12 w-full items-center justify-center rounded-2xl bg-sky-500 px-8 text-sm font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600 sm:w-auto">Mulai Cari Partner</button>
                                    </form>
                                @endif
                                <a href="{{ route('matchmaking.index') }}" class="inline-flex h-12 w-full items-center justify-center rounded-2xl border border-sky-200 bg-white px-6 text-sm font-bold text-slate-700 transition hover:bg-sky-50 sm:w-auto">Atur Profil</a>
                            </div>
                        </div>
                    </main>

                    <aside class="border-t border-sky-100 bg-white p-5 lg:border-l lg:border-t-0">
                        <div class="rounded-[1.75rem] border border-sky-200 bg-sky-50/70 p-5">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Profil Matching</p>
                            <h4 class="mt-2 font-outfit text-xl font-extrabold text-slate-950">{{ auth()->user()->name }}</h4>
                            <div class="mt-5 space-y-3">
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Subjek</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $profile?->primary_subject ?? 'Belum diisi' }}</p>
                                </div>
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Target</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $profile?->goal ?? 'Belum diisi' }}</p>
                                </div>
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Kuota</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ auth()->user()->match_credits }} match tersisa</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-[1.75rem] border border-sky-200 bg-white p-5">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">Aturan Singkat</p>
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                                <p>Mulai untuk masuk antrean cepat.</p>
                                <p>Next untuk ganti partner.</p>
                                <p>Stop untuk keluar dari antrean atau menutup sesi.</p>
                                <p>Report dan block tersedia saat sesi aktif.</p>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        @endif

        @if ($isSearching)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const statusUrl = @json(route('matchmaking.roulette.status'));
                    let isChecking = false;

                    const checkRouletteStatus = async () => {
                        if (isChecking || document.hidden) {
                            return;
                        }

                        isChecking = true;

                        try {
                            const response = await fetch(statusUrl, {
                                headers: {
                                    Accept: 'application/json',
                                },
                                credentials: 'same-origin',
                            });

                            if (!response.ok) {
                                return;
                            }

                            const data = await response.json();

                            if (data.matched && data.redirect_url) {
                                window.location.href = data.redirect_url;
                            }
                        } catch (error) {
                            // Polling is best-effort; the next interval will try again.
                        } finally {
                            isChecking = false;
                        }
                    };

                    checkRouletteStatus();
                    window.setInterval(checkRouletteStatus, 2000);
                    document.addEventListener('visibilitychange', checkRouletteStatus);
                });
            </script>
        @endif

        @if ($activeMatch)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const statusUrl = @json(route('matchmaking.roulette.status'));
                    const currentMatchId = {{ $activeMatch->id }};
                    let isChecking = false;

                    const checkMatchStatus = async () => {
                        if (isChecking || document.hidden) {
                            return;
                        }

                        isChecking = true;

                        try {
                            const response = await fetch(statusUrl, {
                                headers: {
                                    Accept: 'application/json',
                                },
                                credentials: 'same-origin',
                            });

                            if (!response.ok) {
                                return;
                            }

                            const data = await response.json();

                            if (data.latest_match_id === currentMatchId && data.latest_match_status !== 'active') {
                                window.location.href = @json(route('matchmaking.roulette'));
                            }
                        } catch (error) {
                            // Polling is best-effort; the next interval will try again.
                        } finally {
                            isChecking = false;
                        }
                    };

                    window.setInterval(checkMatchStatus, 2500);
                    document.addEventListener('visibilitychange', checkMatchStatus);
                });
            </script>
        @endif
    </div>
</x-app-layout>
