<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">Pengaturan Akun</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">
                Profil
            </h2>
            <p class="mt-2 text-sm text-slate-300/80">Kelola identitas akun, keamanan, dan kontrol akses dari satu halaman yang rapi.</p>
        </div>
    </x-slot>

    <div class="space-y-6 py-4 md:py-6">
        

        <div class="mx-auto max-w-7xl space-y-6 sm:px-2 lg:px-4">
            @if (session('billing_status'))
                <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-5 py-4 text-sm font-semibold text-cyan-800">
                    {{ session('billing_status') }}
                </div>
            @endif

            <section class="grid gap-4 lg:grid-cols-2">
                <article class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/85 p-5 text-slate-950 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Batas Ruang Kelas</p>
                            <h3 class="mt-3 font-outfit text-2xl font-extrabold text-slate-950">{{ $limitStats['room_remaining'] }} tersisa</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $limitStats['owned_room_count'] }} dari {{ $limitStats['room_limit'] }} ruang kelas sudah dibuat.</p>
                            <p class="mt-2 text-xs font-semibold text-slate-500">
                                @if ($user->plan_expires_at)
                                    Aktif sampai {{ $user->plan_expires_at->format('d M Y') }}
                                @elseif ($user->isPremium())
                                    Paket premium tanpa tanggal kedaluwarsa
                                @else
                                    Tingkatkan paket untuk membuka batas tambahan
                                @endif
                            </p>
                        </div>
                        <span class="rounded-2xl bg-sky-100 px-4 py-2 text-sm font-extrabold text-sky-700">{{ strtoupper($user->plan) }}</span>
                    </div>
                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-sky-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-cyan-400" style="width: {{ $limitStats['room_percent'] }}%"></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs font-semibold text-slate-500">
                        <span>Terpakai</span>
                        <span>{{ $limitStats['room_percent'] }}%</span>
                    </div>
                </article>

                <article class="overflow-hidden rounded-[1.75rem] border border-cyan-200 bg-white/85 p-5 text-slate-950 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-700">Kredit Pencarian Teman</p>
                            <h3 class="mt-3 font-outfit text-2xl font-extrabold text-slate-950">{{ $limitStats['match_remaining'] }} tersisa</h3>
                            <p class="mt-1 text-sm text-slate-600">Kuota pencarian teman belajar dari paket {{ $user->plan }}.</p>
                        </div>
                        <span class="rounded-2xl bg-cyan-100 px-4 py-2 text-sm font-extrabold text-cyan-700">{{ $limitStats['match_remaining'] }} / {{ $limitStats['match_allowance'] }}</span>
                    </div>
                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-cyan-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-400" style="width: {{ $limitStats['match_percent'] }}%"></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs font-semibold text-slate-500">
                        <span>Sisa kredit</span>
                        <span>{{ $limitStats['match_percent'] }}%</span>
                    </div>
                </article>
            </section>

            <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/85 text-slate-950 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
                <div class="flex items-center justify-between border-b border-sky-100 p-5">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Tagihan</p>
                        <h3 class="mt-2 font-outfit text-xl font-extrabold text-slate-950">Pembayaran Pakasir</h3>
                    </div>
                    <a href="{{ route('billing.index') }}" class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-bold text-sky-700 transition hover:bg-sky-100">Lihat Riwayat</a>
                </div>

                <div class="divide-y divide-sky-100">
                    @forelse ($recentPayments as $payment)
                        <div class="flex flex-col gap-2 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-slate-950">{{ $payment->plan_name }} - {{ $payment->order_id }}</p>
                                <p class="mt-1 text-sm text-slate-600">Rp {{ number_format($payment->amount, 0, ',', '.') }} pada {{ $payment->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-extrabold {{ $payment->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ strtoupper($payment->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-slate-600">Belum ada pembayaran. Pilih paket premium dari halaman harga.</div>
                    @endforelse
                </div>
            </section>

            <div class="glass-panel accent-card-cyan rounded-[1.75rem] p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('pages.user.profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="glass-panel accent-card-violet rounded-[1.75rem] p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('pages.user.profile.partials.update-password-form')
                </div>
            </div>

            <div class="glass-panel rounded-[1.75rem] p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('pages.user.profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
