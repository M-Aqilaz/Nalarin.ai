<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                Manajemen User
            </h2>
            <p class="text-sm text-zinc-400 mt-1">Daftar semua pengguna terdaftar dan status mereka.</p>
        </div>
    </x-slot>

    <div class="glass-panel p-6 rounded-2xl border border-white/5 bg-zinc-900/50">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 text-xs uppercase tracking-wider text-zinc-500 font-semibold">
                        <th class="py-3 px-4">Nama User</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Bergabung Pada</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-zinc-800/30 transition">
                            <td class="py-4 px-4 font-medium text-zinc-200">{{ $user->name }}</td>
                            <td class="py-4 px-4 text-zinc-400">{{ $user->email }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->role === 'admin' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->is_active ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                    {{ $user->is_active ? 'active' : 'suspended' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-zinc-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->is_active)
                                        <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('Suspend user ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-zinc-800 text-zinc-300 hover:bg-red-500/20 hover:text-red-400 transition border border-transparent hover:border-red-500/30">
                                                Suspend
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" onsubmit="return confirm('Aktifkan kembali user ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-zinc-800 text-zinc-300 hover:bg-green-500/20 hover:text-green-400 transition border border-transparent hover:border-green-500/30">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-500">Belum ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 md:hidden">
            @forelse($users as $user)
                <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-medium text-zinc-200">{{ $user->name }}</p>
                            <p class="text-sm text-zinc-400 mt-1 break-all">{{ $user->email }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->role === 'admin' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' }}">
                            {{ $user->role }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-zinc-500">Status</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->is_active ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                            {{ $user->is_active ? 'active' : 'suspended' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-zinc-500">Bergabung</span>
                        <span class="text-zinc-300">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="pt-2">
                        @if($user->is_active)
                            <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('Suspend user ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full text-xs px-3 py-2.5 rounded-lg bg-zinc-800 text-zinc-300 hover:bg-red-500/20 hover:text-red-400 transition border border-transparent hover:border-red-500/30">
                                    Suspend
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" onsubmit="return confirm('Aktifkan kembali user ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full text-xs px-3 py-2.5 rounded-lg bg-zinc-800 text-zinc-300 hover:bg-green-500/20 hover:text-green-400 transition border border-transparent hover:border-green-500/30">
                                    Aktifkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-zinc-500">Belum ada data user.</div>
            @endforelse
        </div>
        
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</x-admin-layout>
