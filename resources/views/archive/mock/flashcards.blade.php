<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center border border-pink-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                    Flashcards: Kosakata Bahasa Inggris
                </h2>
                <p class="text-sm text-gray-400 mt-1">Kartu 1 dari 15 • Ketuk kartu untuk membalik</p>
            </div>
        </div>
    </x-slot>

    <!-- Custom Style for Flip effect -->
    <style>
        .perspective-1000 { perspective: 1000px; }
        .transform-style-3d { transform-style: preserve-3d; }
        .backface-hidden { backface-visibility: hidden; }
        .rotate-y-180 { transform: rotateY(180deg); }
        .card-container:hover .card-flip,
        .card-container.flipped .card-flip { transform: rotateY(180deg); }
    </style>

    <div class="max-w-3xl mx-auto py-12 flex flex-col items-center">
        
        <!-- Flashcard -->
        <div class="w-full max-w-xl h-80 perspective-1000 cursor-pointer card-container" onclick="this.classList.toggle('flipped')">
            <div class="card-flip w-full h-full relative transition-transform duration-700 transform-style-3d shadow-2xl">
                
                <!-- Front Page (Term) -->
                <div class="absolute inset-0 w-full h-full glass-panel rounded-3xl border border-white/10 flex flex-col items-center justify-center backface-hidden p-8">
                    <p class="absolute top-6 left-6 text-xs font-bold tracking-wider text-pink-400 uppercase">Istilah / Front</p>
                    <svg class="absolute top-6 right-6 w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                    
                    <h2 class="text-4xl md:text-5xl font-bold font-outfit text-white text-center">Serendipity</h2>
                </div>

                <!-- Back Page (Definition) -->
                <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-pink-600 to-purple-700 rounded-3xl border border-white/10 flex flex-col items-center justify-center backface-hidden rotate-y-180 p-8 shadow-[0_0_30px_rgba(219,39,119,0.3)]">
                    <p class="absolute top-6 left-6 text-xs font-bold tracking-wider text-pink-200 uppercase">Definisi / Back</p>
                    
                    <div class="text-center">
                        <h3 class="text-2xl font-bold font-outfit text-white mb-4 italic">"Penemuan sesuatu yang menyenangkan tanpa sengaja."</h3>
                        <p class="text-pink-100/80 text-sm">Contoh: Bertemu teman lama di kedai kopi yang baru pertama kali kamu kunjungi.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Controls -->
        <div class="flex items-center gap-6 mt-12">
            <button class="w-12 h-12 rounded-full border border-white/10 bg-white/5 hover:bg-white/10 text-white flex items-center justify-center transition shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <span class="text-gray-400 font-medium font-outfit text-lg w-16 text-center">1 / 15</span>
            <button class="w-12 h-12 rounded-full bg-pink-600 hover:bg-pink-500 text-white flex items-center justify-center transition shadow-[0_0_15px_rgba(219,39,119,0.4)]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        <!-- Spaced Repetition Buttons (Only show when flipped normally, but we mock it here) -->
        <div class="grid grid-cols-4 gap-3 mt-8 w-full max-w-lg">
            <button class="py-2 px-4 rounded-xl border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 text-red-500 font-semibold text-xs tracking-wide transition">Lagi (1m)</button>
            <button class="py-2 px-4 rounded-xl border border-orange-500/30 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 font-semibold text-xs tracking-wide transition">Sulit (5m)</button>
            <button class="py-2 px-4 rounded-xl border border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20 text-blue-500 font-semibold text-xs tracking-wide transition">Baik (1h)</button>
            <button class="py-2 px-4 rounded-xl border border-green-500/30 bg-green-500/10 hover:bg-green-500/20 text-green-500 font-semibold text-xs tracking-wide transition">Mudah (4d)</button>
        </div>

    </div>
</x-app-layout>
