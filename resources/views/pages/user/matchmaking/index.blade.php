<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-fuchsia-100/90">Study Matching</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">Temukan Partner Belajar</h2>
            <p class="mt-2 text-sm text-slate-300/80">Cari teman belajar baru berdasarkan topik, gaya belajar, dan tujuan yang lebih relevan dengan ritmemu.</p>
        </div>
    </x-slot>
    <div class="space-y-6">
        @php
            $profileEnabled = $user->studyProfile?->is_matchmaking_enabled ?? false;
            $nalaMood = $activeMatch ? 'happy' : ($profileEnabled ? 'flat' : 'sad');
            $nalaTitle = $activeMatch ? 'Kamu punya match aktif' : ($profileEnabled ? 'Profil siap dipakai' : 'Profil matching belum aktif');
            $nalaMessage = $activeMatch
                ? 'Partner belajar sudah ada. Buka match aktif dan jangan dibiarkan menggantung.'
                : ($profileEnabled
                    ? 'Profilmu sudah bisa dipakai untuk roulette. Pastikan goal dan gaya belajar tidak kosong biar hasilnya lebih nyambung.'
                    : 'Aktifkan profil matching dulu. Nala tidak bisa mencarikan partner kalau kamu belum kasih arahan yang jelas.');
        @endphp

        <x-nala-guide :mood="$nalaMood" :title="$nalaTitle" :message="$nalaMessage" :action-label="$activeMatch ? 'Buka Match' : 'Study Roulette'" :action-url="$activeMatch ? route('matches.show', $activeMatch) : route('matchmaking.roulette')" compact />

        <section class="feature-hero">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="user-kicker text-[11px] text-fuchsia-100/90">Meaningful Matching</p>
                <h3 class="mt-3 font-outfit text-2xl font-semibold text-white">Siapkan profilmu, lalu masuk ke mode partner cepat.</h3>
                <p class="mt-3 text-sm text-slate-100/80">Halaman ini sekarang difokuskan untuk menyiapkan identitas belajar. Proses mencari partner dipindahkan ke `Study Roulette` supaya alurnya lebih cepat dan tidak terasa seperti mengisi formulir.</p>
            </div>
            <a href="{{ route('matchmaking.roulette') }}" class="user-primary-button inline-flex items-center justify-center px-5 py-3 text-sm sm:w-auto">Study Roulette</a>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="glass-panel accent-card-violet rounded-[1.75rem] p-5 md:p-6 space-y-6">
            @if (session('status'))
                <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
            @endif
            <div>
                <h3 class="font-outfit text-lg font-semibold text-white">Profil Matching</h3>
                <p class="mt-1 text-sm text-slate-300/70">Profil ini dipakai untuk memasangkan partner belajar yang relevan.</p>
            </div>
            <form method="POST" action="{{ route('matchmaking.profile.update') }}" class="space-y-4">
                @csrf
                <input name="education_level" value="{{ old('education_level', $user->studyProfile?->education_level) }}" placeholder="Jenjang" class="glass-input w-full px-4 py-3">
                <input name="primary_subject" value="{{ old('primary_subject', $user->studyProfile?->primary_subject) }}" placeholder="Mapel utama" class="glass-input w-full px-4 py-3">
                <input name="goal" value="{{ old('goal', $user->studyProfile?->goal) }}" placeholder="Target belajar" class="glass-input w-full px-4 py-3">
                <input name="study_style" value="{{ old('study_style', $user->studyProfile?->study_style) }}" placeholder="Gaya belajar" class="glass-input w-full px-4 py-3">
                <input name="availability" value="{{ old('availability', $user->studyProfile?->availability) }}" placeholder="Ketersediaan waktu" class="glass-input w-full px-4 py-3">
                <textarea name="bio" rows="4" placeholder="Bio singkat" class="glass-input w-full px-4 py-3">{{ old('bio', $user->studyProfile?->bio) }}</textarea>
                <label class="flex items-start gap-3 text-sm text-slate-200"><input type="checkbox" name="is_matchmaking_enabled" value="1" class="mt-1 shrink-0" @checked(old('is_matchmaking_enabled', $user->studyProfile?->is_matchmaking_enabled ?? true))> <span>Aktifkan study matching</span></label>
                <button class="user-primary-button w-full px-5 py-3 sm:w-auto">Simpan Profil</button>
            </form>
        </section>

        <section class="glass-panel accent-card-pink rounded-[1.75rem] p-5 md:p-6 space-y-6">
            <div class="flex flex-col gap-4">
                <div>
                    <h3 class="font-outfit text-lg font-semibold text-white">Study Roulette</h3>
                    <p class="mt-1 text-sm text-slate-300/70">Masuk antrean cepat untuk menemukan partner belajar baru. Kuota tersisa: {{ auth()->user()->match_credits }}</p>
                </div>
                @if ($activeMatch)
                    <a href="{{ route('matchmaking.roulette') }}" class="user-primary-button inline-flex w-full px-4 py-2.5 text-sm sm:w-auto">Lanjutkan Roulette</a>
                @endif
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-5">
                <p class="text-sm leading-6 text-slate-300/80">Mode ini tidak meminta topik, jenjang, atau tipe sesi sebagai input wajib. Sistem akan memakai profil yang kamu simpan di sebelah kiri, lalu mencarikan partner lewat queue cepat.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                    <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Profil aktif</p>
                    <p class="mt-2 text-sm text-white">{{ ($user->studyProfile?->is_matchmaking_enabled ?? false) ? 'Siap dipakai' : 'Belum aktif' }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                    <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Sesi aktif</p>
                    <p class="mt-2 text-sm text-white">{{ $activeMatch ? 'Ada match berjalan' : 'Belum ada match aktif' }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('matchmaking.roulette') }}" class="user-primary-button inline-flex w-full items-center justify-center px-6 py-3 text-sm sm:w-auto">Buka Study Roulette</a>
                @if ($activeMatch)
                    <a href="{{ route('matches.show', $activeMatch) }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-6 py-3 text-sm font-medium text-white transition hover:bg-white/[0.08] sm:w-auto">Buka Match Aktif</a>
                @endif
            </div>
        </section>
        </div>
    </div>
</x-app-layout>
