<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-lg hover:bg-white/5 text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                        Ringkasan: Sejarah Kemerdekaan Indonesia.pdf
                    </h2>
                    <span class="px-2.5 py-1 rounded-full border border-green-500/30 bg-green-500/10 text-xs font-medium text-green-400 whitespace-nowrap flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                        Selesai Diproses
                    </span>
                </div>
                <p class="text-sm text-gray-400 mt-1">Dihasilkan oleh AI dari dokumen setebal 24 halaman.</p>
            </div>
        </div>
    </x-slot>

    <div class="flex flex-col lg:flex-row gap-6 h-full">
        
        <!-- Main Content Area: Summary -->
        <div class="flex-1 glass-panel rounded-2xl border border-white/5 flex flex-col h-[calc(100vh-140px)]">
            
            <div class="p-4 border-b border-white/5 flex gap-2">
                <button class="px-4 py-2 text-sm font-medium border-b-2 border-purple-500 text-purple-400">Ringkasan Utama</button>
                <button class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-white">Transkrip Penuh</button>
                <button class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-white">Mind Map</button>
            </div>

            <div class="p-6 overflow-y-auto prose prose-invert max-w-none text-gray-300">
                <h3 class="text-white font-outfit text-xl mb-4 font-bold border-b border-white/10 pb-2">Poin-poin Kunci 🔑</h3>
                <ul class="space-y-2 mb-8 list-none pl-0">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span><strong>Kekosongan Kekuasaan:</strong> Jepang menyerah pada Sekutu tanggal 14 Agustus 1945 setelah pengeboman Hiroshima dan Nagasaki, menciptakan <i>vacuum of power</i> di Indonesia.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span><strong>Peristiwa Rengasdengklok:</strong> Golongan muda (Wikana, Sukarni, Chaerul Saleh) menculik Soekarno-Hatta pada 16 Agustus 1945 agar tidak terpengaruh Jepang dan segera memproklamasikan kemerdekaan.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span><strong>Perumusan Teks:</strong> Disusun di rumah Laksamana Maeda pada malam 16 Agustus 1945 oleh Soekarno, Hatta, dan Ahmad Soebardjo. Naskah diketik oleh Sayuti Melik.</span>
                    </li>
                </ul>

                <h3 class="text-white font-outfit text-xl mt-8 mb-4 font-bold border-b border-white/10 pb-2">Penjelasan Detail 📖</h3>
                <p>
                    Proklamasi Kemerdekaan Indonesia dilaksanakan pada hari Jumat, 17 Agustus 1945 tahun Masehi, atau tanggal 17 Agustus 2605 menurut tahun Jepang, yang dibacakan oleh Soekarno didampingi oleh Mohammad Hatta di sebuah kediaman di Jalan Pegangsaan Timur No. 56, Jakarta Pusat.
                </p>
                <div class="bg-purple-900/20 border border-purple-500/20 rounded-xl p-4 my-6">
                    <p class="text-sm text-purple-200 italic m-0 relative">
                        <span class="absolute -top-2 -left-1 text-2xl text-purple-500/40 font-serif">"</span>
                        Kami bangsa Indonesia dengan ini menjatakan Kemerdekaan Indonesia. Hal-hal jang mengenai pemindahan kekoeasaan d.l.l., diselenggarakan dengan tjara seksama dan dalam tempo jang sesingkat-singkatnja.
                    </p>
                </div>
                <p>
                    Setelah pembacaan teks proklamasi tersebut, bendera pusaka Merah Putih yang dijahit oleh Ibu Fatmawati dikibarkan, disusul dengan nyanyian lagu kebangsaan "Indonesia Raya". Peristiwa ini menandai dimulainya perlawanan diplomatik dan bersenjata dari Revolusi Nasional Indonesia.
                </p>

                <!-- Mind Map Mock -->
                <h3 class="text-white font-outfit text-xl mt-12 mb-6 font-bold border-b border-white/10 pb-2">Peta Konsep (Mind Map) 🗺️</h3>
                <div class="w-full bg-gray-900 border border-white/10 rounded-2xl p-6 relative overflow-hidden flex flex-col items-center justify-center min-h-[400px]">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 mix-blend-overlay"></div>
                    
                    <!-- Central Node -->
                    <div class="z-10 px-6 py-3 bg-purple-600 rounded-full font-bold text-white shadow-[0_0_20px_rgba(168,85,247,0.4)] mb-8">
                        Proklamasi Kemerdekaan RI
                    </div>
                    
                    <!-- Connectors & Sub-nodes -->
                    <div class="z-10 flex gap-4 md:gap-12 relative w-full justify-center">
                        <!-- Lines drawn with CSS borders -->
                        <div class="absolute top-[-32px] left-1/2 -translate-x-1/2 w-3/4 h-8 border-t-2 border-l-2 border-r-2 border-purple-500/30 rounded-t-xl pointer-events-none"></div>
                        <div class="absolute top-[-32px] left-1/2 -translate-x-1/2 w-px h-8 bg-purple-500/30 pointer-events-none"></div>

                        <!-- Node 1 -->
                        <div class="flex flex-col items-center w-1/3 text-center">
                            <div class="px-4 py-2 bg-gray-800 border border-purple-500/40 rounded-xl text-sm font-medium text-purple-200">
                                Vacuum of Power
                            </div>
                            <div class="h-6 w-px bg-purple-500/20 my-2"></div>
                            <div class="text-[10px] text-gray-400 max-w-[120px]">
                                Menyerahnya Jepang tanpa syarat ke Sekutu (14 Agt 1945)
                            </div>
                        </div>

                        <!-- Node 2 -->
                        <div class="flex flex-col items-center w-1/3 text-center mt-6">
                            <div class="px-4 py-2 bg-gray-800 border border-purple-500/40 rounded-xl text-sm font-medium text-purple-200">
                                Rengasdengklok
                            </div>
                            <div class="h-6 w-px bg-purple-500/20 my-2"></div>
                            <div class="text-[10px] text-gray-400 max-w-[120px]">
                                Penculikan Soekarno-Hatta oleh golongan muda (16 Agt 1945)
                            </div>
                        </div>

                        <!-- Node 3 -->
                        <div class="flex flex-col items-center w-1/3 text-center">
                            <div class="px-4 py-2 bg-gray-800 border border-purple-500/40 rounded-xl text-sm font-medium text-purple-200">
                                Perumusan Teks
                            </div>
                            <div class="h-6 w-px bg-purple-500/20 my-2"></div>
                            <div class="text-[10px] text-gray-400 max-w-[120px]">
                                Rumah Laksamana Maeda, diketik oleh Sayuti Melik
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar / Tools Area Menu -->
        <div class="w-full lg:w-80 flex flex-col gap-4">
            
            <a href="{{ route('feature.chat') }}" class="glass-panel p-5 rounded-2xl border border-white/5 hover:border-purple-500/50 hover:bg-purple-500/5 transition group">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <h4 class="font-outfit font-semibold text-white mb-1">Tanya AI Tentang Materi Ini</h4>
                <p class="text-xs text-gray-400">Punya pertanyaan yang belum jelas? Diskusikan dengan tutor interaktif.</p>
            </a>

            <a href="{{ route('feature.flashcards') }}" class="glass-panel p-5 rounded-2xl border border-white/5 hover:border-pink-500/50 hover:bg-pink-500/5 transition group">
                <div class="w-10 h-10 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h4 class="font-outfit font-semibold text-white mb-1">Pelajari Flashcards</h4>
                <p class="text-xs text-gray-400">15 Kartu berhasil dibuat dari dokumen ini untuk hafalan.</p>
            </a>

            <a href="{{ route('feature.quiz') }}" class="glass-panel p-5 rounded-2xl border border-white/5 hover:border-green-500/50 hover:bg-green-500/5 transition group">
                <div class="w-10 h-10 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-outfit font-semibold text-white mb-1">Mulai Kuis</h4>
                <p class="text-xs text-gray-400">Uji seberapa paham kamu. Tersedia 10 soal pilihan ganda.</p>
            </a>

            <div class="flex gap-2 mt-auto pt-4">
                <button class="flex-1 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-medium transition cursor-pointer flex items-center justify-center gap-2 border border-white/5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Aksi Lainnya
                </button>
            </div>

        </div>

    </div>
</x-app-layout>
