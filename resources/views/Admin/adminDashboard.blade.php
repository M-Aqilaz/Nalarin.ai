<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                Selamat Datang, admin! 👋
            </h2>
            <p class="text-sm text-gray-400 mt-1">Ringkasan sistem hari ini.</p>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('feature.upload') }}" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-medium text-sm transition shadow-[0_0_15px_rgba(168,85,247,0.4)] flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Materi Baru
        </a>    
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Stats Section (Left / Top) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stat Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="glass-panel p-4 rounded-2xl border border-white/5">
                    <p class="text-xs text-gray-400 mb-1 font-medium uppercase">Total User</p>
                    <p class="text-2xl font-bold font-outfit text-white">{{ $stats['total_users'] }}</p>
                </div>
                <div class="glass-panel p-4 rounded-2xl border border-white/5">
                    <p class="text-xs text-green-400 mb-1 font-medium uppercase">User Aktif (Hari Ini)</p>
                    <p class="text-2xl font-bold font-outfit text-white">{{ $stats['active_users'] }}</p>
                </div>
                <div class="glass-panel p-4 rounded-2xl border border-white/5">
                    <p class="text-xs text-gray-400 mb-1 font-medium uppercase">Jumlah Materi</p>
                    <p class="text-2xl font-bold font-outfit text-white">{{ $stats['total_documents'] }}</p>
                </div>
                <div class="glass-panel p-4 rounded-2xl border border-white/5 bg-gradient-to-br from-purple-900/40 to-transparent">
                    <p class="text-xs text-purple-300 mb-1 font-medium uppercase">Request AI</p>
                    <div class="flex items-center gap-2">
                        <p class="text-2xl font-bold font-outfit text-white">{{ $stats['total_ai_requests'] }}</p>
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="glass-panel rounded-2xl border border-white/5">
                <div class="p-5 border-b border-white/5 flex items-center justify-between">
                    <h3 class="font-outfit font-semibold text-lg text-white">Aktivitas Terakhir</h3>
                    <a href="#" class="text-sm text-purple-400 hover:text-purple-300">Lihat Semua</a>
                </div>
                <div class="divide-y divide-white/5">
                    
                    <!-- Item 1 -->
                    <div class="p-4 flex items-center justify-between hover:bg-white/5 transition cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="font-medium text-white text-sm">Sejarah Kemerdekaan Indonesia.pdf</p>
                                <p class="text-xs text-gray-500 mt-0.5">Diringkas 2 jam yang lalu</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded border border-purple-500/20 bg-purple-500/10 text-[10px] uppercase font-bold text-purple-300 tracking-wider">Ringkasan</span>
                    </div>

                    <!-- Item 2 -->
                    <div class="p-4 flex items-center justify-between hover:bg-white/5 transition cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="font-medium text-white text-sm">Biologi Bab 3: Sel - CrashCourse</p>
                                <p class="text-xs text-gray-500 mt-0.5">Video YouTube • Kemarin</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded border border-green-500/20 bg-green-500/10 text-[10px] uppercase font-bold text-green-300 tracking-wider">Kuis 80/100</span>
                    </div>

                    <!-- Item 3 -->
                    <div class="p-4 flex items-center justify-between hover:bg-white/5 transition cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div>
                                <p class="font-medium text-white text-sm">Kosakata Bahasa Inggris Sehari-hari</p>
                                <p class="text-xs text-gray-500 mt-0.5">Sedang dipelajari</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded border border-pink-500/20 bg-pink-500/10 text-[10px] uppercase font-bold text-pink-300 tracking-wider">Flashcards</span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Feature Usage Chart (Right Column) -->
        <div class="space-y-6">
            <div class="glass-panel p-6 rounded-2xl border border-white/5 bg-gradient-to-b from-gray-800/80 to-gray-900/80 relative overflow-hidden h-full flex flex-col justify-between">
                <div>
                    <h3 class="font-outfit font-bold text-xl text-white mb-2">Statistik Penggunaan Fitur</h3>
                    <p class="text-sm text-gray-400 mb-6">Grafik fitur yang paling sering digunakan oleh user.</p>
                    
                    <div class="w-full relative h-64">
                        <canvas id="featureUsageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('featureUsageChart').getContext('2d');
            
            // Data passed from controller
            const featuresData = @json($featureUsages);
            
            const labels = featuresData.map(f => f.feature_name);
            const data = featuresData.map(f => f.click_count);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Klik',
                        data: data,
                        backgroundColor: 'rgba(168, 85, 247, 0.5)',
                        borderColor: 'rgba(168, 85, 247, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: 'rgba(255, 255, 255, 0.9)'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-admin-layout>
