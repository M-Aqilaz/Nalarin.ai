<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Pelajarin.ai - Platform Belajar AI untuk Siswa Indonesia</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:500,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .font-outfit { font-family: 'Outfit', sans-serif; }
            .font-inter { font-family: 'Inter', sans-serif; }
            .glass-card {
                background: rgba(31, 41, 55, 0.4);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            .gradient-text {
                background: linear-gradient(135deg, #a855f7 0%, #3b82f6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
                100% { transform: translateY(0px); }
            }
            .blob {
                position: absolute;
                filter: blur(80px);
                z-index: -1;
                opacity: 0.4;
                border-radius: 50%;
            }
        </style>
    </head>
    <body class="font-inter antialiased bg-gray-950 text-gray-100 overflow-x-hidden selection:bg-purple-500/30">
        
        <!-- Background Blobs -->
        <div class="blob bg-purple-600 w-96 h-96 top-0 left-0 -translate-x-1/2 -translate-y-1/2"></div>
        <div class="blob bg-blue-600 w-[30rem] h-[30rem] top-1/4 right-0 translate-x-1/3"></div>
        <div class="blob bg-indigo-600 w-80 h-80 bottom-0 left-1/4 translate-y-1/2"></div>

        <!-- Navbar -->
        <nav class="fixed w-full z-50 glass-card border-b-0 border-white/5 top-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-xl">
                            P
                        </div>
                        <span class="font-outfit font-bold text-2xl tracking-tight text-white">Pelajarin<span class="text-purple-400">.ai</span></span>
                    </div>
                    <div class="hidden md:flex space-x-8">
                        <a href="#fitur" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Fitur</a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Harga</a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Testimoni</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full bg-white text-gray-950 text-sm font-semibold hover:bg-gray-200 transition shadow-[0_0_20px_rgba(255,255,255,0.2)]">Mulai Belajar</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden items-center flex flex-col justify-center min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-purple-500/30 bg-purple-500/10 text-purple-300 text-xs font-medium mb-8">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                    </span>
                    Pelajarin.ai V2.0 Kini Tersedia
                </div>
                
                <h1 class="font-outfit text-5xl md:text-7xl font-extrabold tracking-tight text-white mb-6 leading-tight">
                    Transformasi Cara Belajarmu <br />
                    dengan <span class="gradient-text">Kecerdasan Buatan</span>
                </h1>
                
                <p class="mt-4 max-w-2xl text-lg md:text-xl text-gray-400 mx-auto mb-10 leading-relaxed">
                    Satu platform untuk semua kebutuhan belajarmu. Upload materi apapun, dapatkan ringkasan, flashcard, dan kuis dalam sekejap tanpa repot.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('feature.upload') }}" class="px-8 py-4 rounded-full bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold text-lg hover:shadow-[0_0_30px_rgba(168,85,247,0.4)] hover:scale-105 transition-all duration-300">
                        Mulai Belajar Sekarang
                    </a>
                    <a href="#demo" class="px-8 py-4 rounded-full border border-gray-600 bg-gray-800/50 text-white font-semibold text-lg hover:bg-gray-700/50 transition-all duration-300 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Lihat Demo
                    </a>
                </div>

                <!-- Dashboard Preview Mockup -->
                <div class="mt-20 relative max-w-5xl mx-auto animate-float">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-transparent to-transparent z-10"></div>
                    <div class="glass-card p-2 rounded-2xl md:rounded-[2rem] border border-white/10 shadow-2xl relative overflow-hidden">
                        <!-- Mac Window Controls -->
                        <div class="flex gap-2 px-4 py-3 border-b border-white/5 bg-white/5">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="bg-gray-900 rounded-b-xl aspect-video bg-cover bg-center opacity-80" style="background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop');">
                            <div class="w-full h-full bg-gray-900/60 backdrop-blur-sm flex items-center justify-center">
                                <div class="text-center p-8">
                                    <div class="w-16 h-16 bg-purple-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-purple-500/50">
                                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-white font-outfit">AI Sedang Menyusun Ringkasan...</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </main>

        <!-- Features Section -->
        <section id="fitur" class="py-24 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="font-outfit text-3xl md:text-5xl font-bold text-white mb-4">Belajar Lebih Cerdas, Bukan Lebih Keras</h2>
                    <p class="text-gray-400 text-lg">Semua alat yang kamu butuhkan untuk memahami materi dengan cepat dan menyenangkan.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Feature 1 -->
                    <div class="glass-card p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-500/20 transition-colors border border-blue-500/20">
                            <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 font-outfit">Ringkasan Otomatis</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Ubah dokumen ratusan halaman menjadi poin-poin penting yang mudah dipahami dalam hitungan detik.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="glass-card p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-500/20 transition-colors border border-purple-500/20">
                            <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 font-outfit">AI Tutor 24/7</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Tanyakan apa saja tentang materimu. AI kami siap menjelaskan konsep sulit kapan saja layaknya guru pribadi.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="glass-card p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="w-14 h-14 bg-pink-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-pink-500/20 transition-colors border border-pink-500/20">
                            <svg class="w-7 h-7 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 font-outfit">Smart Flashcards</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Hafalkan istilah penting dengan metode Spaced Repetition yang dibuktikan secara ilmiah efektif.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="glass-card p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="w-14 h-14 bg-green-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-500/20 transition-colors border border-green-500/20">
                            <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 font-outfit">Interactive Quiz</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Uji pemahamanmu dengan kuis pilihan ganda yang dibuat otomatis dari materi yang kamu pelajari.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 relative z-10">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-br from-purple-900/50 to-blue-900/50 border border-purple-500/20 rounded-[3rem] p-12 text-center relative overflow-hidden glass-card">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
                    <h2 class="font-outfit text-3xl md:text-5xl font-bold text-white mb-6 relative z-10">Siap Revolusi Cara Belajarmu?</h2>
                    <p class="text-xl text-purple-200 mb-10 max-w-2xl mx-auto relative z-10">Gabung dengan ribuan siswa cerdas lainnya. Mulai gratis, upgrade kapan saja. Tidak perlu kartu kredit.</p>
                    <a href="{{ route('feature.upload') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-full bg-white text-gray-900 font-bold text-lg hover:bg-gray-100 hover:scale-105 transition-all duration-300 shadow-[0_0_40px_rgba(255,255,255,0.3)] relative z-10">
                        Masuk Ruang Belajar
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-white/5 py-12 relative z-10 mt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-xs">P</div>
                    <span class="font-outfit font-bold text-xl tracking-tight text-white">Pelajarin.ai</span>
                </div>
                <div class="text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Pelajarin.ai Clone. Built with Laravel + Tailwind.
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-white transition">Kebijakan Privasi</a>
                    <a href="#" class="text-gray-500 hover:text-white transition">Syarat & Ketentuan</a>
                </div>
            </div>
        </footer>
    </body>
</html>
