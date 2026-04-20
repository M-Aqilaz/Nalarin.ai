<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Group Chat Kelas</h2>
            <p class="text-sm text-gray-400 mt-1">Gabung room belajar publik atau buat room sendiri untuk cohort dan komunitas.</p>
        </div>
    </x-slot>
    <div class="grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-6">
        <section class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
            <div class="p-5 border-b border-white/5"><h3 class="font-outfit text-lg font-semibold text-white">Daftar Room</h3></div>
            <div class="divide-y divide-white/5">
                @forelse ($rooms as $room)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <a href="{{ route('rooms.show', $room) }}" class="text-white font-medium">{{ $room->name }}</a>
                                <p class="text-sm text-gray-400 mt-1">{{ $room->topic }} • {{ $room->members_count }} member • {{ $room->visibility }}</p>
                                <p class="text-sm text-gray-500 mt-2">{{ $room->description }}</p>
                            </div>
                            <form method="POST" action="{{ route('rooms.join', $room) }}">
                                @csrf
                                <button type="submit" class="rounded-xl bg-purple-600 px-4 py-2 text-white text-sm">Join</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-sm text-gray-400">Belum ada room belajar.</div>
                @endforelse
            </div>
        </section>

        <section class="glass-panel rounded-2xl border border-white/5 p-6 space-y-6">
            <div>
                <h3 class="font-outfit text-lg font-semibold text-white">Buat Room Baru</h3>
                <p class="text-sm text-gray-400 mt-1">Plan gratis dibatasi oleh quota room.</p>
            </div>
            @if (session('status'))
                <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('rooms.store') }}" method="POST" class="space-y-4">
                @csrf
                <input name="name" placeholder="Nama room" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required>
                <input name="topic" placeholder="Topik utama" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required>
                <textarea name="description" rows="4" placeholder="Deskripsi room" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white"></textarea>
                <select name="visibility" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white"><option value="public">Public</option><option value="private">Private</option></select>
                <input name="max_members" type="number" min="5" max="100" value="30" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                <button type="submit" class="w-full rounded-xl bg-purple-600 py-3 text-white font-medium">Buat Room</button>
            </form>
        </section>
    </div>
</x-app-layout>
