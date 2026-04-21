<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Study Matching</h2>
            <p class="text-sm text-gray-400 mt-1">Temukan partner belajar baru berdasarkan topik dan profil belajarmu.</p>
        </div>
    </x-slot>
    <div class="grid grid-cols-1 xl:grid-cols-[0.95fr_1.05fr] gap-6">
        <section class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6 space-y-6">
            @if (session('status'))
                <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
            @endif
            <div>
                <h3 class="font-outfit text-lg font-semibold text-white">Profil Matching</h3>
                <p class="text-sm text-gray-400 mt-1">Profil ini dipakai untuk memasangkan partner belajar yang relevan.</p>
            </div>
            <form method="POST" action="{{ route('matchmaking.profile.update') }}" class="space-y-4">
                @csrf
                <input name="education_level" value="{{ old('education_level', $user->studyProfile?->education_level) }}" placeholder="Jenjang" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                <input name="primary_subject" value="{{ old('primary_subject', $user->studyProfile?->primary_subject) }}" placeholder="Mapel utama" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                <input name="goal" value="{{ old('goal', $user->studyProfile?->goal) }}" placeholder="Target belajar" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                <input name="study_style" value="{{ old('study_style', $user->studyProfile?->study_style) }}" placeholder="Gaya belajar" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                <input name="availability" value="{{ old('availability', $user->studyProfile?->availability) }}" placeholder="Ketersediaan waktu" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                <textarea name="bio" rows="4" placeholder="Bio singkat" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">{{ old('bio', $user->studyProfile?->bio) }}</textarea>
                <label class="flex items-start gap-3 text-sm text-gray-300"><input type="checkbox" name="is_matchmaking_enabled" value="1" class="mt-1" @checked(old('is_matchmaking_enabled', $user->studyProfile?->is_matchmaking_enabled ?? true))> <span>Aktifkan study matching</span></label>
                <button class="w-full rounded-xl bg-white/10 px-5 py-3 text-white font-medium sm:w-auto">Simpan Profil</button>
            </form>
        </section>

        <section class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-outfit text-lg font-semibold text-white">Cari Teman Belajar</h3>
                    <p class="text-sm text-gray-400 mt-1">Kuota tersisa: {{ auth()->user()->match_credits }}</p>
                </div>
                @if ($activeMatch)
                    <a href="{{ route('matches.show', $activeMatch) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-purple-600 px-4 py-2.5 text-sm text-white sm:w-auto">Lanjutkan Match</a>
                @endif
            </div>

            @if ($queue)
                <div class="rounded-2xl border border-blue-500/30 bg-blue-500/10 p-5">
                    <p class="text-white font-medium">Sedang mencari partner untuk topik {{ $queue->selected_topic }}</p>
                    <p class="text-sm text-blue-100/80 mt-1">Antrean aktif sampai {{ optional($queue->expires_at)->format('H:i') ?? '-' }}</p>
                    <form method="POST" action="{{ route('matchmaking.cancel') }}" class="mt-4">@csrf<button class="w-full rounded-xl bg-white/10 px-4 py-2.5 text-sm text-white sm:w-auto">Batalkan Antrean</button></form>
                </div>
            @else
                <form method="POST" action="{{ route('matchmaking.search') }}" class="space-y-4">
                    @csrf
                    <input name="selected_topic" placeholder="Topik match, contoh: Biologi Sel" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required>
                    <input name="preferred_level" placeholder="Jenjang yang dicari" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                    <input name="preferred_session_type" placeholder="Tipe sesi, contoh: diskusi santai" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                    <button class="w-full rounded-xl bg-purple-600 px-6 py-3 text-white font-medium sm:w-auto">Mulai Matching</button>
                </form>
            @endif
        </section>
    </div>
</x-app-layout>
