<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                Manajemen Materi
            </h2>
            <p class="text-sm text-zinc-400 mt-1">Pantau dan kelola semua materi yang diunggah oleh user.</p>
        </div>
    </x-slot>

    <div class="glass-panel p-6 rounded-2xl border border-white/5 bg-zinc-900/50">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 text-xs uppercase tracking-wider text-zinc-500 font-semibold">
                        <th class="py-3 px-4">Judul Materi</th>
                        <th class="py-3 px-4">Pengunggah (User)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tanggal Unggah</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-sm">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-zinc-800/30 transition">
                            <td class="py-4 px-4 font-medium text-zinc-200">{{ $doc->title }}</td>
                            <td class="py-4 px-4 text-zinc-400">{{ $doc->user->name ?? 'User Dihapus' }}</td>
                            <td class="py-4 px-4">
                                @php
                                    $statusColors = [
                                        'processed' => 'bg-green-500/10 text-green-400 border border-green-500/20',
                                        'uploaded' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                        'failed' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                                    ];
                                    $color = $statusColors[$doc->status] ?? 'bg-zinc-500/10 text-zinc-400';
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $color }}">
                                    {{ $doc->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-zinc-500">{{ $doc->created_at->format('d M Y H:i') }}</td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini secara permanen? Ringkasan, chat, flashcards, dan kuis terkait juga akan ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-zinc-800 text-zinc-300 hover:bg-red-500/20 hover:text-red-400 transition border border-transparent hover:border-red-500/30">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500">Belum ada materi yang diunggah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 md:hidden">
            @forelse($documents as $doc)
                @php
                    $statusColors = [
                        'processed' => 'bg-green-500/10 text-green-400 border border-green-500/20',
                        'uploaded' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                        'failed' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                    ];
                    $color = $statusColors[$doc->status] ?? 'bg-zinc-500/10 text-zinc-400';
                @endphp
                <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-4 space-y-3">
                    <div>
                        <p class="font-medium text-zinc-200">{{ $doc->title }}</p>
                        <p class="text-sm text-zinc-400 mt-1">{{ $doc->user->name ?? 'User Dihapus' }}</p>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-zinc-500">Status</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $color }}">
                            {{ $doc->status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-zinc-500">Tanggal Upload</span>
                        <span class="text-zinc-300 text-right">{{ $doc->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini secara permanen? Ringkasan, chat, flashcards, dan kuis terkait juga akan ikut terhapus.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-xs px-3 py-2.5 rounded-lg bg-zinc-800 text-zinc-300 hover:bg-red-500/20 hover:text-red-400 transition border border-transparent hover:border-red-500/30">
                            Hapus
                        </button>
                    </form>
                </div>
            @empty
                <div class="py-8 text-center text-zinc-500">Belum ada materi yang diunggah.</div>
            @endforelse
        </div>
        
        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    </div>
</x-admin-layout>
