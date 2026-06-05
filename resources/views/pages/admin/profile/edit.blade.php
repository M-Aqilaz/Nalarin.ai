<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">
                Edit Profile Admin
            </h2>
            <p class="text-sm text-zinc-400 mt-1">Kelola nama, email, password, dan akses akun admin.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <section class="glass-panel rounded-2xl border border-white/5 p-6 xl:col-span-1">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-purple-500/20 bg-purple-500/10">
                        <span class="font-outfit text-2xl font-bold text-purple-200">{{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate font-outfit text-xl font-semibold text-white">{{ $user->name }}</p>
                        <p class="mt-1 truncate text-sm text-zinc-400">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950/50 p-4">
                        <p class="text-xs uppercase tracking-wide text-zinc-500">Role</p>
                        <p class="mt-2 text-sm font-semibold text-purple-200">{{ ucfirst($user->role) }}</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950/50 p-4">
                        <p class="text-xs uppercase tracking-wide text-zinc-500">Status</p>
                        <p class="mt-2 text-sm font-semibold {{ $user->is_active ? 'text-green-300' : 'text-red-300' }}">
                            {{ $user->is_active ? 'Active' : 'Suspended' }}
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-admin-user-deletion')"
                    class="mt-6 inline-flex w-full items-center justify-center rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-2.5 text-sm font-medium text-red-200 transition hover:bg-red-500/15"
                >
                    Hapus Akun
                </button>
            </section>

            <div class="space-y-6 xl:col-span-2">
                <section class="glass-panel rounded-2xl border border-white/5 p-6">
                    @include('pages.user.profile.partials.update-profile-information-form')
                </section>

                <section class="glass-panel rounded-2xl border border-white/5 p-6">
                    @include('pages.user.profile.partials.update-password-form')
                </section>
            </div>
        </div>
    </div>

    <x-modal name="confirm-admin-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-5 bg-zinc-950 p-6 text-white">
            @csrf
            @method('delete')

            <div>
                <h2 class="font-outfit text-lg font-semibold text-white">Hapus akun admin?</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-400">Akun ini akan dihapus permanen beserta data terkait. Masukkan password untuk melanjutkan.</p>
            </div>

            <div>
                <x-input-label for="admin_delete_password" value="Password" class="sr-only" />
                <x-text-input
                    id="admin_delete_password"
                    name="password"
                    type="password"
                    class="glass-input mt-1 block w-full border-0 bg-transparent text-white"
                    placeholder="Password"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-white/10 bg-white/[0.08] px-4 py-2.5 text-sm text-white transition hover:bg-white/[0.12]">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-red-500/14 px-4 py-2.5 text-sm font-medium text-red-200 transition hover:bg-red-500/20">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>
