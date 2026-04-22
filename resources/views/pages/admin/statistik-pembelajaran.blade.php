<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                Statistik Pembelajaran
            </h2>
            <p class="text-sm text-gray-400 mt-1">Data dan performa belajar user.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="glass-panel rounded-2xl border border-white/5">
            <div class="p-5 border-b border-white/5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-500/20 flex items-center justify-center text-orange-400">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="font-outfit font-semibold text-lg text-white">Statistik Utama</h3>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white/5 rounded-xl p-4 border border-white/5 flex items-center gap-4 hover:bg-white/10 transition cursor-default">
                    <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 shrink-0 shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                        <span class="font-bold text-lg font-outfit">{{ $learningStats['overall_score'] }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium uppercase">Overall Score</p>
                        <p class="text-xl font-bold font-outfit text-white">A-</p>
                    </div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 border border-white/5 flex items-center gap-4 hover:bg-white/10 transition cursor-default">
                    <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center text-green-400 shrink-0 shadow-[0_0_15px_rgba(34,197,94,0.2)]">
                        <span class="font-bold text-lg font-outfit">A+</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium uppercase">Rata-rata Skor Quiz</p>
                        <p class="text-xl font-bold font-outfit text-white">{{ $learningStats['avg_quiz_score'] }}%</p>
                    </div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 border border-white/5 flex items-center gap-4 hover:bg-white/10 transition cursor-default">
                    <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 shrink-0 shadow-[0_0_15px_rgba(168,85,247,0.2)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium uppercase">Fitur Paling Dipakai</p>
                        <p class="text-lg font-bold font-outfit text-white line-clamp-1" title="{{ $learningStats['most_used_feature'] }}">{{ $learningStats['most_used_feature'] }}</p>
                    </div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 border border-white/5 flex items-center gap-4 hover:bg-white/10 transition cursor-default">
                    <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center text-orange-400 shrink-0 shadow-[0_0_15px_rgba(249,115,22,0.2)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium uppercase">Aktivitas Belajar</p>
                        <p class="text-xl font-bold font-outfit text-white">{{ $learningStats['learning_activity'] }} Jam</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-panel p-6 rounded-2xl border border-white/5 bg-gradient-to-b from-gray-800/80 to-gray-900/80 mt-6">
            <h3 class="font-outfit font-bold text-xl text-white mb-2">Distribusi Nilai</h3>
            <p class="text-sm text-gray-400 mb-6">Grafik skor user di seluruh platform.</p>
            <div class="w-full relative h-64 border border-dashed border-white/10 rounded-xl overflow-hidden flex items-center justify-center bg-white/5">
                <p class="text-gray-500 font-medium text-sm">Bar Chart / Grafik menyusul</p>
            </div>
        </div>
    </div>
</x-admin-layout>
