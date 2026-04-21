<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Thread Chat</h2>
            <p class="text-sm text-gray-400 mt-1">Fondasi diskusi materi per thread.</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-6">
        <section class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
            <div class="p-5 border-b border-white/5">
                <h3 class="font-outfit text-lg font-semibold text-white">Daftar Thread</h3>
            </div>
            <div class="divide-y divide-white/5">
                @forelse ($threads as $thread)
                    <a href="{{ route('chat.show', $thread) }}" class="block p-4 hover:bg-white/5 transition">
                        <p class="text-white font-medium">{{ $thread->title }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $thread->material?->title ?? 'Tanpa materi' }} | {{ $thread->messages_count }} pesan</p>
                    </a>
                @empty
                    <div class="p-4 text-sm text-gray-400">Belum ada thread chat.</div>
                @endforelse
            </div>
        </section>

        <section class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6">
            <h3 class="font-outfit text-lg font-semibold text-white mb-5">Buat Thread Baru</h3>
            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200 mb-4">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('chat.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Judul Thread</label>
                    <input name="title" type="text" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Terkait Materi</label>
                    <select name="material_id" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white">
                        <option value="">Tanpa materi</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}">{{ $material->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Pesan Pembuka</label>
                    <textarea name="opening_message" rows="5" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white"></textarea>
                </div>
                <button type="submit" class="w-full rounded-xl bg-purple-600 py-3 text-white font-medium">Buat Thread</button>
            </form>
        </section>
    </div>
</x-app-layout>
