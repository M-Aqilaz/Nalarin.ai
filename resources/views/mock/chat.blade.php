<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center border border-purple-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
            <div>
                <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                    AI Tutor Khusus
                </h2>
                <p class="text-sm text-gray-400 mt-1">Diskusikan materi, minta penjelasan, atau selesaikan soal matematika.</p>
            </div>
        </div>
    </x-slot>

    <div class="glass-panel border border-white/5 rounded-2xl h-[calc(100vh-160px)] flex flex-col relative overflow-hidden">
        
        <!-- Background Blur -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-600/5 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Chat History -->
        <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-6 scroll-smooth z-10">
            
            <!-- AI Message -->
            <div class="flex gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-white">AI</span>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl rounded-tl-sm px-5 py-3.5 max-w-[85%]">
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Halo! Saya adalah AI Tutor Nalarin.ai. Saya bisa membantu meringkas materi, menjelaskan rumus, atau menemani kamu belajar untuk ujian besok. Ada materi yang sedang kamu pelajari sekarang?
                    </p>
                </div>
            </div>

            <!-- User Message -->
            <div class="flex gap-4 flex-row-reverse">
                <img src="https://ui-avatars.com/api/?name=Siswa&background=random" class="w-8 h-8 rounded-full shrink-0 border border-gray-600">
                <div class="bg-purple-600 rounded-2xl rounded-tr-sm px-5 py-3.5 max-w-[85%] shadow-lg">
                    <p class="text-white text-sm leading-relaxed">
                        Iya nih, dari dokumen "Sejarah Kemerdekaan" tadi, tolong jelaskan apa yang dimaksud dengan vacuum of power?
                    </p>
                </div>
            </div>

            <!-- AI Message -->
            <div class="flex gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-white">AI</span>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl rounded-tl-sm px-5 py-3.5 max-w-[85%] prose prose-invert prose-sm">
                    <p class="text-gray-300 leading-relaxed m-0">
                        <strong>Vacuum of Power</strong> atau <i>kekosongan kekuasaan</i> dalam konteks kemerdekaan Indonesia terjadi ketika Jepang menyerah tanpa syarat kepada Sekutu pada 14 Agustus 1945.
                    </p>
                    <ul class="text-gray-300 text-sm mt-3 mb-0 space-y-1">
                        <li>Jepang secara resmi tidak lagi berkuasa atas Indonesia.</li>
                        <li>Pasukan Sekutu belum datang untuk mengambil alih kendali di Indonesia.</li>
                    </ul>
                    <p class="text-gray-300 leading-relaxed mt-3 mb-0">
                        Kondisi "kosong" inilah yang dimanfaatkan oleh para pejuang (terutama golongan muda) untuk segera memproklamasikan kemerdekaan sebelum Sekutu datang (yang biasanya didampingi oleh Belanda).
                    </p>
                </div>
            </div>

            <!-- User Message -->
            <div class="flex gap-4 flex-row-reverse">
                <img src="https://ui-avatars.com/api/?name=Siswa&background=random" class="w-8 h-8 rounded-full shrink-0 border border-gray-600">
                <div class="bg-purple-600 rounded-2xl rounded-tr-sm px-5 py-3.5 max-w-[85%] shadow-lg">
                    <p class="text-white text-sm leading-relaxed">
                        Ohh paham. Lalu bagaimana Sekutu merespon saat tahu Indonesia sudah merdeka?
                    </p>
                </div>
            </div>

            <!-- AI Typing Indicator -->
            <div class="flex gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-white">AI</span>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl rounded-tl-sm px-5 py-4 w-16 flex items-center justify-center gap-1">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></span>
                </div>
            </div>

            <!-- Spacer for scroll -->
            <div class="h-4"></div>
        </div>

        <!-- Chat Input Area -->
        <div class="p-4 border-t border-white/10 bg-gray-950/50 backdrop-blur-md z-10">
            <div class="max-w-4xl mx-auto flex items-end gap-2 bg-gray-900 border border-white/10 rounded-2xl p-2 pb-2 focus-within:ring-2 focus-within:ring-purple-500/50 transition">
                <button class="p-2 text-gray-400 hover:text-purple-400 transition rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                </button>
                
                <textarea rows="1" class="flex-1 bg-transparent border-0 text-white placeholder-gray-500 focus:ring-0 resize-none py-3 h-[50px] max-h-32 leading-tight" placeholder="Ketik pertanyaanmu di sini..."></textarea>
                
                <button class="p-3 bg-purple-600 hover:bg-purple-500 text-white rounded-xl shadow-[0_0_15px_rgba(168,85,247,0.4)] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </div>
            <div class="text-center mt-2">
                <p class="text-[10px] text-gray-500">AI dapat menghasilkan informasi yang kurang akurat. Selalu periksa kembali kemiripan dengan jurnal silabus.</p>
            </div>
        </div>
    </div>
</x-app-layout>
