<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center border border-green-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                    Kuis: Sejarah Kemerdekaan
                </h2>
                <p class="text-sm text-gray-400 mt-1">Soal 4 dari 10 • Waktu berjalan: 04:21</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <button class="px-5 py-2.5 rounded-xl border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 text-red-400 font-medium text-sm transition">
            Akhiri Kuis
        </button>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        
        <!-- Progress Bar -->
        <div class="w-full h-2 bg-gray-800 rounded-full mb-8 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-green-500 to-emerald-400 w-2/5 rounded-full relative">
                <div class="absolute right-0 top-0 bottom-0 w-4 bg-white/20 blur-[2px]"></div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="glass-panel rounded-3xl border border-white/5 p-8 md:p-10 relative overflow-hidden shadow-2xl">
            <!-- decorative -->
            <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-green-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex gap-4">
                <div class="w-12 h-12 shrink-0 rounded-full bg-white/5 border border-white/10 flex items-center justify-center font-bold text-xl text-gray-300 font-outfit">
                    4
                </div>
                <div>
                    <h3 class="text-xl md:text-2xl font-bold text-white mb-6 leading-relaxed font-outfit">
                        Dimana Soekarno dan Hatta diamankan oleh golongan muda pada tanggal 16 Agustus 1945 sebelum proklamasi kemerdekaan?
                    </h3>

                    <!-- Options -->
                    <div class="space-y-3">
                        
                        <!-- Option A -->
                        <label class="block relative cursor-pointer group">
                            <input type="radio" name="answer_4" class="peer sr-only">
                            <div class="w-full p-4 rounded-xl border-2 border-white/10 bg-white/5 text-gray-300 font-medium transition-all group-hover:border-white/20 group-hover:bg-white/10 peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-white flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-gray-800 border border-white/10 flex items-center justify-center text-sm font-bold peer-checked:bg-green-500 peer-checked:border-green-500">A</div>
                                <span>Jalan Pegangsaan Timur No. 56</span>
                            </div>
                        </label>

                        <!-- Option B -->
                        <label class="block relative cursor-pointer group">
                            <input type="radio" name="answer_4" class="peer sr-only" checked>
                            <div class="w-full p-4 rounded-xl border-2 border-white/10 bg-white/5 text-gray-300 font-medium transition-all group-hover:border-white/20 group-hover:bg-white/10 peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-white flex items-center gap-4 shadow-[0_0_15px_rgba(34,197,94,0.15)]">
                                <div class="w-8 h-8 rounded-lg border border-green-500/0 flex items-center justify-center text-sm font-bold bg-green-500 text-white">B</div>
                                <span>Rengasdengklok, Karawang</span>
                            </div>
                        </label>

                        <!-- Option C -->
                        <label class="block relative cursor-pointer group">
                            <input type="radio" name="answer_4" class="peer sr-only">
                            <div class="w-full p-4 rounded-xl border-2 border-white/10 bg-white/5 text-gray-300 font-medium transition-all group-hover:border-white/20 group-hover:bg-white/10 peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-white flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-gray-800 border border-white/10 flex items-center justify-center text-sm font-bold peer-checked:bg-green-500 peer-checked:border-green-500">C</div>
                                <span>Rumah Laksamana Maeda</span>
                            </div>
                        </label>

                        <!-- Option D -->
                        <label class="block relative cursor-pointer group">
                            <input type="radio" name="answer_4" class="peer sr-only">
                            <div class="w-full p-4 rounded-xl border-2 border-white/10 bg-white/5 text-gray-300 font-medium transition-all group-hover:border-white/20 group-hover:bg-white/10 peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-white flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-gray-800 border border-white/10 flex items-center justify-center text-sm font-bold peer-checked:bg-green-500 peer-checked:border-green-500">D</div>
                                <span>Gedung Djawa Hokokai</span>
                            </div>
                        </label>

                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center justify-between mt-10 pt-6 border-t border-white/10">
                        <button class="px-6 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 text-white font-medium text-sm transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Sebelumnya
                        </button>
                        
                        <button class="px-8 py-2.5 rounded-xl bg-green-500 hover:bg-green-400 text-white font-semibold shadow-[0_0_15px_rgba(34,197,94,0.4)] transition flex items-center gap-2 text-sm">
                            Selanjutnya
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>
