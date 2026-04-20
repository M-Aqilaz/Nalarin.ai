<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">{{ $room->name }}</h2>
            <p class="text-sm text-gray-400 mt-1">{{ $room->topic }} • {{ $room->visibility }} • {{ $room->members->count() }} anggota</p>
        </div>
    </x-slot>
    <div class="grid grid-cols-1 lg:grid-cols-[1.4fr_0.6fr] gap-6">
        <section class="glass-panel rounded-2xl border border-white/5 p-6 space-y-4">
            @if (session('status'))
                <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
            @endif
            <div class="max-h-[28rem] overflow-y-auto space-y-4 pr-1">
                @forelse ($messages as $message)
                    <div class="{{ $message->user_id === auth()->id() ? 'ml-auto bg-purple-600/20 border-purple-500/20' : 'mr-auto bg-white/5 border-white/10' }} max-w-3xl rounded-2xl border p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">{{ $message->user->name }}</p>
                        <p class="text-sm leading-7 text-gray-200 whitespace-pre-line">{{ $message->content }}</p>
                    </div>
                @empty
                    <div class="text-sm text-gray-400">Belum ada pesan di room ini.</div>
                @endforelse
            </div>
            <form action="{{ route('rooms.messages.store', $room) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="content" rows="4" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required></textarea>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Kirim pesan ke seluruh anggota aktif room.</span>
                    <button type="submit" class="rounded-xl bg-purple-600 px-6 py-3 text-white font-medium">Kirim</button>
                </div>
            </form>
        </section>
        <aside class="glass-panel rounded-2xl border border-white/5 p-6">
            <div class="flex items-center justify-between">
                <h3 class="font-outfit text-lg font-semibold text-white">Anggota</h3>
                <form method="POST" action="{{ route('rooms.leave', $room) }}">@csrf<button class="text-sm text-red-300">Keluar</button></form>
            </div>
            <div class="mt-4 space-y-3">
                @foreach ($room->members as $member)
                    @if ($member->status === 'active')
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-white text-sm font-medium">{{ $member->user->name }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $member->role }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </aside>
    </div>
</x-app-layout>
