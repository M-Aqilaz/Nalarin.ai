<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                Unggah Materi Baru
            </h2>
            <p class="text-sm text-gray-400 mt-1">Upload PDF, Word, atau Paste Link YouTube untuk diringkas AI.</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8">
        <div class="glass-panel rounded-3xl border border-white/5 p-8 relative overflow-hidden">
            <!-- Decorative Background Element -->
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-purple-600/10 rounded-full blur-3xl"></div>
            
            <form action="{{ route('feature.summary') }}" method="GET" class="relative z-10">
                
                <!-- Tab Pilihan -->
                <div class="flex space-x-2 border-b border-white/10 mb-8 pb-3">
                    <button type="button" class="px-4 py-2 text-sm font-medium border-b-2 border-purple-500 text-purple-400 transition-colors">
                        Dokumen / Berkas
                    </button>
                    <button type="button" class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-white transition-colors">
                        Link URL / YouTube
                    </button>
                    <button type="button" class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-white transition-colors">
                        Teks Langsung
                    </button>
                </div>

                <!-- Input Nama Materi -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Judul Materi (Opsional)</label>
                    <input type="text" id="title" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-transparent transition-all" placeholder="Contoh: Sejarah Kemerdekaan Bab 5">
                </div>

                <!-- Dropzone Uploader -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Pilih File</label>
                    <div class="border-2 border-dashed border-gray-600 rounded-2xl hover:border-purple-500 hover:bg-purple-500/5 transition-colors group cursor-pointer">
                        <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                            <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <p class="text-white font-medium mb-1">Klik untuk upload atau drag and drop</p>
                            <p class="text-sm text-gray-400">PDF, PPTX, DOCX, MP3, MP4 (Maks. 50MB)</p>
                        </div>
                        <input type="file" class="hidden">
                    </div>
                </div>

                <!-- Opsi AI -->
                <div class="mb-8 p-5 bg-gray-900/50 rounded-2xl border border-white/5">
                    <h4 class="font-medium text-white mb-4 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Opsi Pemrosesan AI
                    </h4>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 cursor-pointer transition border border-transparent hover:border-white/5">
                            <input type="checkbox" checked class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-purple-500 focus:ring-purple-500/50">
                            <span class="text-sm text-gray-300">Buatkan Ringkasan Singkat & Rinci</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 cursor-pointer transition border border-transparent hover:border-white/5">
                            <input type="checkbox" checked class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-purple-500 focus:ring-purple-500/50">
                            <span class="text-sm text-gray-300">Generasi Pertanyaan Kuis (10 Soal Pilihan Ganda)</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 cursor-pointer transition border border-transparent hover:border-white/5">
                            <input type="checkbox" checked class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-purple-500 focus:ring-purple-500/50">
                            <span class="text-sm text-gray-300">Buatkan Flashcards Istilah Penting</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-white/10">
                    <!-- We link this button to summary UI -->
                    <button type="submit" class="px-8 py-3 rounded-xl bg-purple-600 font-semibold text-white hover:bg-purple-500 hover:shadow-[0_0_20px_rgba(168,85,247,0.4)] transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Proses dengan AI
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
