<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                Selamat Datang, Admin
            </h2>
            <p class="text-sm text-gray-400 mt-1">Ringkasan sistem hari ini.</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="glass-panel p-4 rounded-2xl border border-white/5">
                    <p class="text-xs text-gray-400 mb-1 font-medium uppercase">Total User</p>
                    <p class="text-2xl font-bold font-outfit text-white">{{ $stats['total_users'] }}</p>
                </div>
                <div class="glass-panel p-4 rounded-2xl border border-white/5">
                    <p class="text-xs text-green-400 mb-1 font-medium uppercase">User Aktif</p>
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



            <div class="glass-panel rounded-2xl border border-white/5">
                <div class="p-5 border-b border-white/5">
                    <h3 class="font-outfit font-semibold text-lg text-white">Aktivitas Terakhir</h3>
                </div>
                <div class="p-5">
                    <div class="rounded-2xl border border-dashed border-white/10 bg-white/5 p-6">
                        <p class="text-sm font-medium text-white">Panel aktivitas detail belum dihubungkan ke data real-time.</p>
                        <p class="mt-2 text-sm text-gray-400">Statistik utama dan grafik penggunaan fitur sudah memakai data nyata. Aktivitas detail lebih aman ditambahkan setelah sumber event admin ditentukan dengan jelas.</p>
                    </div>
                </div>
            </div>
        </div>

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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('featureUsageChart').getContext('2d');
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
