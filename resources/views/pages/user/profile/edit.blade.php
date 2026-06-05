<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">{{ __('Account Settings') }}</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">
                {{ __('Profile') }}
            </h2>
            <p class="mt-2 text-sm text-slate-300/80">Kelola identitas akun, keamanan, dan kontrol akses dari satu halaman yang lebih konsisten dengan tema workspace.</p>
        </div>
    </x-slot>

    <div class="space-y-6 py-4 md:py-6">
        

        <div class="mx-auto max-w-7xl space-y-6 sm:px-2 lg:px-4">
            <section class="grid gap-4 lg:grid-cols-2">
                <article class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/85 p-5 text-slate-950 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Room Limit</p>
                            <h3 class="mt-3 font-outfit text-2xl font-extrabold text-slate-950">{{ $limitStats['room_remaining'] }} tersisa</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $limitStats['owned_room_count'] }} dari {{ $limitStats['room_limit'] }} room sudah dibuat.</p>
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
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-700">Match Credit</p>
                            <h3 class="mt-3 font-outfit text-2xl font-extrabold text-slate-950">{{ $limitStats['match_remaining'] }} tersisa</h3>
                            <p class="mt-1 text-sm text-slate-600">Kuota study matching dari paket {{ $user->plan }}.</p>
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
