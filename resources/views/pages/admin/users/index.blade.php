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

        <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <a href="{{ route('admin.users.index') }}" class="rounded-2xl border p-4 transition {{ $selectedPlan === 'all' ? 'border-purple-500/40 bg-purple-500/10' : 'border-zinc-800 bg-zinc-950/50 hover:border-zinc-700' }}">
                <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Semua User</p>
                <p class="mt-2 font-outfit text-2xl font-bold text-purple-700">{{ $planStats['all'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['plan' => 'free']) }}" class="rounded-2xl border p-4 transition {{ $selectedPlan === 'free' ? 'border-cyan-500/40 bg-cyan-500/10' : 'border-zinc-800 bg-zinc-950/50 hover:border-zinc-700' }}">
                <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Freemium</p>
                <p class="mt-2 font-outfit text-2xl font-bold text-cyan-700">{{ $planStats['free'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['plan' => 'premium']) }}" class="rounded-2xl border p-4 transition {{ $selectedPlan === 'premium' ? 'border-amber-500/40 bg-amber-500/10' : 'border-zinc-800 bg-zinc-950/50 hover:border-zinc-700' }}">
                <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Paid</p>
                <p class="mt-2 font-outfit text-2xl font-bold text-amber-700">{{ $planStats['premium'] }}</p>
            </a>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 text-xs uppercase tracking-wider text-zinc-500 font-semibold">
                        <th class="py-3 px-4">Nama User</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Paket</th>
                        <th class="py-3 px-4">Kuota</th>
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
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->role === 'admin' ? 'bg-purple-500/10 text-purple-700 border border-purple-500/20' : 'bg-blue-500/10 text-blue-700 border border-blue-500/20' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->plan === 'premium' ? 'bg-amber-500/10 text-amber-700 border border-amber-500/20' : 'bg-cyan-500/10 text-cyan-700 border border-cyan-500/20' }}">
                                    {{ $user->plan === 'premium' ? 'Paid' : 'Freemium' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-zinc-400">
                                <div>Room: <span class="text-zinc-200">{{ $user->room_limit }}</span></div>
                                <div>Match: <span class="text-zinc-200">{{ $user->match_credits }}</span></div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->is_active ? 'bg-green-500/10 text-green-700 border border-green-500/20' : 'bg-red-500/10 text-red-600 border border-red-500/20' }}">
                                    {{ $user->is_active ? 'active' : 'suspended' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-zinc-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->role !== 'admin')
                                        <form action="{{ route('admin.users.plan', $user->id) }}" method="POST" onsubmit="return confirm('Ubah paket user ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="plan" value="{{ $user->plan === 'premium' ? 'free' : 'premium' }}">
                                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 border border-sky-200 hover:bg-sky-100 hover:text-sky-800 transition">
                                                {{ $user->plan === 'premium' ? 'Jadikan Freemium' : 'Jadikan Paid' }}
                                            </button>
                                        </form>
                                    @endif
                                    @if($user->is_active)
                                        <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('Suspend user ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 hover:text-red-700 transition">
                                                Suspend
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" onsubmit="return confirm('Aktifkan kembali user ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-green-50 text-green-600 border border-green-200 hover:bg-green-100 hover:text-green-700 transition">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-zinc-500">Belum ada data user.</td>
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
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->role === 'admin' ? 'bg-purple-500/10 text-purple-700 border border-purple-500/20' : 'bg-blue-500/10 text-blue-700 border border-blue-500/20' }}">
                            {{ $user->role }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-zinc-500">Status</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->is_active ? 'bg-green-500/10 text-green-700 border border-green-500/20' : 'bg-red-500/10 text-red-600 border border-red-500/20' }}">
                            {{ $user->is_active ? 'active' : 'suspended' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-zinc-500">Paket</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide {{ $user->plan === 'premium' ? 'bg-amber-500/10 text-amber-700 border border-amber-500/20' : 'bg-cyan-500/10 text-cyan-700 border border-cyan-500/20' }}">
                            {{ $user->plan === 'premium' ? 'Paid' : 'Freemium' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl border border-zinc-800 bg-zinc-900/70 p-3">
                            <span class="text-zinc-500">Room Limit</span>
                            <p class="mt-1 font-semibold text-zinc-200">{{ $user->room_limit }}</p>
                        </div>
                        <div class="rounded-xl border border-zinc-800 bg-zinc-900/70 p-3">
                            <span class="text-zinc-500">Match Credit</span>
                            <p class="mt-1 font-semibold text-zinc-200">{{ $user->match_credits }}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-zinc-500">Bergabung</span>
                        <span class="text-zinc-300">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="space-y-2 pt-2">
                        @if($user->role !== 'admin')
                            <form action="{{ route('admin.users.plan', $user->id) }}" method="POST" onsubmit="return confirm('Ubah paket user ini?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="plan" value="{{ $user->plan === 'premium' ? 'free' : 'premium' }}">
                                <button type="submit" class="w-full text-xs px-3 py-2.5 rounded-lg bg-sky-50 text-sky-700 border border-sky-200 hover:bg-sky-100 hover:text-sky-800 transition">
                                    {{ $user->plan === 'premium' ? 'Jadikan Freemium' : 'Jadikan Paid' }}
                                </button>
                            </form>
                        @endif
                        @if($user->is_active)
                            <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('Suspend user ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full text-xs px-3 py-2.5 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 hover:text-red-700 transition">
                                    Suspend
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" onsubmit="return confirm('Aktifkan kembali user ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full text-xs px-3 py-2.5 rounded-lg bg-green-50 text-green-600 border border-green-200 hover:bg-green-100 hover:text-green-700 transition">
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
