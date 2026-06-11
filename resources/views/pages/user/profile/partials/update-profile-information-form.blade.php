<section>
    <header>
        <h2 class="font-outfit text-xl font-semibold text-white">
            Informasi Profil
        </h2>

        <p class="mt-2 text-sm text-slate-300/75">
            Perbarui informasi profil dan alamat email akunmu.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama" class="text-slate-200" />
            <x-text-input id="name" name="name" type="text" class="glass-input mt-1 block w-full border-0 bg-transparent text-white" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" class="text-slate-200" />
            <x-text-input id="email" name="email" type="email" class="glass-input mt-1 block w-full border-0 bg-transparent text-white" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-slate-200">
                        Alamat emailmu belum diverifikasi.

                        <button form="send-verification" class="rounded-md text-sm text-cyan-100 underline focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-0">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-300">
                            Tautan verifikasi baru sudah dikirim ke alamat emailmu.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="user-primary-button px-5 py-2.5 text-sm">Simpan</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-300/70"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>
