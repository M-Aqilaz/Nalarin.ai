<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                Monitoring AI Usage
            </h2>
            <p class="text-sm text-gray-400 mt-1">Pantau penggunaan dan performa AI hari ini.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="glass-panel rounded-2xl border border-white/5">
            <div class="p-5 border-b border-white/5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="font-outfit font-semibold text-lg text-white">Monitoring AI Usage</h3>
            </div>
            <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-purple-300/70 mb-1 font-medium uppercase tracking-wider">Total Request AI</p>
                    <p class="text-2xl font-bold font-outfit text-white">{{ $aiStats['total_requests'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-red-400/70 mb-1 font-medium uppercase tracking-wider">Error AI</p>
                    <p class="text-2xl font-bold font-outfit text-white">{{ $aiStats['errors'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-blue-300/70 mb-1 font-medium uppercase tracking-wider">Response Time</p>
                    <p class="text-2xl font-bold font-outfit text-white">{{ $aiStats['avg_response_time'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-green-300/70 mb-1 font-medium uppercase tracking-wider">Usage per User</p>
                    <p class="text-2xl font-bold font-outfit text-white">{{ $aiStats['usage_per_user'] }}</p>
                </div>
            </div>
        </div>

        <div class="glass-panel p-6 rounded-2xl border border-white/5 bg-gradient-to-b from-gray-800/80 to-gray-900/80 mt-6">
            <h3 class="font-outfit font-bold text-xl text-white mb-2">Trend AI Requests</h3>
            <p class="text-sm text-gray-400 mb-6">Grafik mock penggunaan 7 hari terakhir.</p>
            <div class="w-full relative h-64 border border-dashed border-white/10 rounded-xl overflow-hidden flex items-center justify-center bg-white/5">
                <p class="text-gray-500 font-medium text-sm">Area Chart / Grafik menyusul</p>
            </div>
        </div>
    </div>
</x-admin-layout>
