<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Notifikasi</h2>
            <p class="text-sm text-gray-400 mt-1">Ringkasan aktivitas terbaru dari AI tutor, room, dan study match.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-outfit text-lg font-semibold text-white">Kotak Masuk</h3>
                    <p class="text-sm text-gray-400 mt-1">Klik notifikasi untuk membuka halaman terkait.</p>
                </div>
                @if (auth()->user()->unreadNotifications()->count() > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button class="rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white">Tandai Semua Dibaca</button>
                    </form>
                @endif
            </div>
        </section>

        <section class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
            <div class="divide-y divide-white/5">
                @forelse ($notifications as $notification)
                    @php($data = $notification->data)
                    <div class="{{ is_null($notification->read_at) ? 'bg-purple-500/5' : 'bg-transparent' }} p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-white font-medium">{{ $data['title'] ?? 'Notifikasi' }}</h4>
                                    @if (is_null($notification->read_at))
                                        <span class="rounded-full border border-purple-400/30 bg-purple-500/10 px-2 py-0.5 text-[10px] uppercase tracking-[0.2em] text-purple-200">Baru</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-gray-300">{{ $data['body'] ?? 'Ada pembaruan baru untuk akun kamu.' }}</p>
                                @if (!empty($data['content']))
                                    <p class="mt-2 text-sm text-gray-500 break-words">{{ $data['content'] }}</p>
                                @endif
                                <p class="mt-3 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row md:shrink-0">
                                @if (!empty($data['url']))
                                    <a href="{{ $data['url'] }}" class="inline-flex items-center justify-center rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-medium text-white">Buka</a>
                                @endif
                                @if (is_null($notification->read_at))
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button class="inline-flex w-full items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white">Tandai Dibaca</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-sm text-gray-400">Belum ada notifikasi untuk akun ini.</div>
                @endforelse
            </div>
        </section>

        <div>
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
