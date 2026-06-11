<section class="space-y-6">
    <header>
        <h2 class="font-outfit text-xl font-semibold text-white">
            Hapus Akun
        </h2>

        <p class="mt-2 text-sm text-slate-300/75">
            Setelah akun dihapus, semua data dan sumber daya yang terkait akan dihapus secara permanen. Pastikan kamu sudah menyimpan informasi penting sebelum melanjutkan.
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-xl bg-red-500/12 px-5 py-2.5 text-sm font-medium text-red-200 transition hover:bg-red-500/18"
    >Hapus Akun</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 bg-slate-950 p-6 text-white">
            @csrf
            @method('delete')

            <h2 class="font-outfit text-lg font-medium text-white">
                Yakin ingin menghapus akun?
            </h2>

            <p class="text-sm text-slate-300/75">
                Setelah akun dihapus, semua data akan hilang secara permanen. Masukkan kata sandi untuk mengonfirmasi penghapusan akun.
            </p>

            <div>
                <x-input-label for="password" value="Kata Sandi" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="glass-input mt-1 block w-full border-0 bg-transparent text-white sm:w-3/4"
                    placeholder="Kata Sandi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-white/10 bg-white/[0.08] px-4 py-2.5 text-sm text-white">
                    Batal
                </button>

                <button type="submit" class="ms-3 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-700 shadow-sm">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
