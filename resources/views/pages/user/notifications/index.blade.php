<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-700">Activity Inbox</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight text-slate-950 md:text-3xl">Notifikasi</h2>
            <p class="mt-2 text-sm text-slate-700">Ringkasan aktivitas terbaru dari AI tutor, room, dan study match dalam satu inbox yang lebih rapi.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="feature-hero border border-cyan-100 bg-white/75 shadow-sm">
            <div class="max-w-3xl">
                <p class="user-kicker text-[11px] text-cyan-700">Stay in Flow</p>
                <p class="mt-3 text-sm font-medium text-slate-700">Notifikasi penting tetap cepat dipindai, jadi kamu bisa langsung lompat ke materi, room, atau pasangan study match yang relevan.</p>
            </div>
        </section>

        <section class="rounded-[1.75rem] border border-cyan-100 bg-white/85 p-5 shadow-sm md:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-outfit text-lg font-semibold text-slate-950">Kotak Masuk</h3>
                    <p class="mt-1 text-sm text-slate-600">Klik notifikasi untuk membuka halaman terkait.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if (auth()->user()->unreadNotifications()->count() > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-sm font-semibold text-cyan-800 transition hover:border-cyan-300 hover:bg-cyan-100">Tandai Semua Dibaca</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.destroy-all') }}" onsubmit="return confirm('Hapus semua notifikasi? Data notifikasi akan dihapus dari database.')" class="inline-flex">
                        @csrf
                        @method('DELETE')
                        <button
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:border-rose-300 hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-45"
                            @disabled($notifications->count() === 0)
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 3h6m-8 4h10m-9 0 .7 12.1A2 2 0 0 0 10.69 21h2.62a2 2 0 0 0 1.99-1.9L16 7m-5 4v6m2-6v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Clear All
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white/90 shadow-sm">
            <div class="divide-y divide-slate-200">
                @forelse ($notifications as $notification)
                    @php($data = $notification->data)
                    <div class="{{ is_null($notification->read_at) ? 'bg-cyan-50/80' : 'bg-white/60' }} p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-slate-950">{{ $data['title'] ?? 'Notifikasi' }}</h4>
                                    @if (is_null($notification->read_at))
                                        <span class="rounded-full border border-cyan-200 bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.2em] text-cyan-800">Baru</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm font-medium text-slate-700">{{ $data['body'] ?? 'Ada pembaruan baru untuk akun kamu.' }}</p>
                                @if (!empty($data['content']))
                                    <p class="mt-2 break-words text-sm text-slate-600">{{ $data['content'] }}</p>
                                @endif
                                <p class="mt-3 text-xs font-medium text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row md:shrink-0">
                                @if (!empty($data['url']))
                                    <a href="{{ $data['url'] }}" class="user-primary-button inline-flex items-center justify-center px-4 py-2.5 text-sm">Buka</a>
                                @endif
                                @if (is_null($notification->read_at))
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-cyan-200 hover:bg-cyan-50">Tandai Dibaca</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-sm font-medium text-slate-600">Belum ada notifikasi untuk akun ini.</div>
                @endforelse
            </div>
        </section>

        <div>
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
