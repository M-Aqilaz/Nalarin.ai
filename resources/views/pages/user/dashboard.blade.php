<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Dashboard Belajar</h2>
            <p class="text-sm text-gray-400 mt-1">Ringkasan AI tools, ruang kelas, dan study matching untuk akunmu.</p>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('feature.upload') }}" class="inline-flex w-full items-center justify-center px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-medium text-sm transition md:w-auto">Materi Baru</a>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="glass-panel p-5 rounded-2xl border border-white/5"><p class="text-xs uppercase tracking-wide text-gray-400">Materi</p><p class="mt-2 text-3xl font-outfit font-bold text-white">{{ $materialCount }}</p></div>
            <div class="glass-panel p-5 rounded-2xl border border-white/5"><p class="text-xs uppercase tracking-wide text-gray-400">Ringkasan</p><p class="mt-2 text-3xl font-outfit font-bold text-white">{{ $summaryCount }}</p></div>
            <div class="glass-panel p-5 rounded-2xl border border-white/5"><p class="text-xs uppercase tracking-wide text-gray-400">Thread AI</p><p class="mt-2 text-3xl font-outfit font-bold text-white">{{ $threadCount }}</p></div>
            <div class="glass-panel p-5 rounded-2xl border border-white/5"><p class="text-xs uppercase tracking-wide text-gray-400">Room Kelas</p><p class="mt-2 text-3xl font-outfit font-bold text-white">{{ $roomCount }}</p></div>
            <div class="glass-panel p-5 rounded-2xl border border-white/5"><p class="text-xs uppercase tracking-wide text-gray-400">Match Aktif</p><p class="mt-2 text-3xl font-outfit font-bold text-white">{{ $activeMatchCount }}</p></div>
        </div>

        <section class="glass-panel rounded-2xl border border-amber-500/20 bg-amber-500/10 p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-amber-200">Plan {{ auth()->user()->plan }}</p>
                <p class="text-lg font-semibold text-white mt-2">Sisa kuota study matching: {{ auth()->user()->match_credits }}</p>
                <p class="text-sm text-amber-100/80 mt-1">Upgrade premium untuk room lebih banyak, match tanpa batas, dan fitur sosial penuh.</p>
            </div>
            <a href="{{ route('pricing') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-white text-gray-950 px-5 py-3 text-sm font-semibold md:w-auto">Lihat Pricing</a>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <section class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-5 border-b border-white/5 flex items-center justify-between">
                    <h3 class="font-outfit text-lg font-semibold text-white">Room Kelas</h3>
                    <a href="{{ route('rooms.index') }}" class="text-sm text-purple-400">Buka room</a>
                </div>
                <div class="divide-y divide-white/5">
                    @forelse ($recentRooms as $room)
                        <a href="{{ route('rooms.show', $room) }}" class="block p-4 hover:bg-white/5 transition">
                            <p class="text-white font-medium">{{ $room->name }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ $room->topic }} | {{ $room->members_count }} member</p>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-gray-400">Belum ikut room.</div>
                    @endforelse
                </div>
            </section>

            <section class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-5 border-b border-white/5 flex items-center justify-between">
                    <h3 class="font-outfit text-lg font-semibold text-white">Materi Terbaru</h3>
                    <a href="{{ route('materials.index') }}" class="text-sm text-purple-400">Lihat semua</a>
                </div>
                <div class="divide-y divide-white/5">
                    @forelse ($recentMaterials as $material)
                        <a href="{{ route('materials.show', $material) }}" class="block p-4 hover:bg-white/5 transition">
                            <p class="text-white font-medium">{{ $material->title }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ $material->status }} | {{ $material->summaries->count() }} ringkasan</p>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-gray-400">Belum ada materi.</div>
                    @endforelse
                </div>
            </section>

            <section class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-5 border-b border-white/5 flex items-center justify-between">
                    <h3 class="font-outfit text-lg font-semibold text-white">Thread AI</h3>
                    <a href="{{ route('feature.chat') }}" class="text-sm text-purple-400">Buka chat</a>
                </div>
                <div class="divide-y divide-white/5">
                    @forelse ($recentThreads as $thread)
                        <a href="{{ route('chat.show', $thread) }}" class="block p-4 hover:bg-white/5 transition">
                            <p class="text-white font-medium">{{ $thread->title }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ $thread->messages_count }} pesan | {{ $thread->material?->title ?? 'Tanpa materi' }}</p>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-gray-400">Belum ada thread chat.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
