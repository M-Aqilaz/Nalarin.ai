<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-sky-700">{{ __('ui.upload_page_kicker') }}</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">{{ __('ui.upload_page_title') }}</h2>
            <p class="mt-2 text-sm text-slate-700">{{ __('ui.upload_page_description') }}</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <x-nala-guide
            mood="happy"
            :title="__('ui.upload_guide_title')"
            :message="__('ui.upload_guide_description')"
            image="images/nala_upload_guide.png"
            image-class="h-40 w-36 rounded-[1.5rem] object-cover object-[center_15%] sm:h-48 sm:w-44"
            compact
        />

        <section class="rounded-[1.75rem] border border-sky-200 bg-white/88 p-5 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
            <div class="max-w-2xl">
                <p class="user-kicker text-[11px] text-sky-700">{{ __('ui.upload_benefit_title') }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-700">{{ __('ui.upload_benefit_description') }}</p>
            </div>
        </section>

        <form
            action="{{ route('materials.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 rounded-[1.75rem] border border-sky-200 bg-white/90 p-8 shadow-[0_18px_38px_rgba(14,116,144,0.12)]"
            x-data="{ uploading: false, fileName: '' }"
            @submit="uploading = true"
        >
            @csrf

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-6 text-slate-700">
                {{ __('ui.upload_supported_description') }}
                @unless (auth()->user()->isPremium())
                    {{ __('ui.upload_free_scan_limit', ['count' => config('services.ocr.free_max_pages', 5)]) }}
                @endunless
            </div>

            <div>
                <label for="title" class="mb-2 block text-sm font-bold text-slate-700">{{ __('ui.material_title') }}</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-slate-950 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" required>
            </div>

            <div>
                <label for="material_file" class="mb-2 block text-sm font-bold text-slate-700">{{ __('ui.material_file') }}</label>
                <input
                    id="material_file"
                    name="material_file"
                    type="file"
                    accept=".txt,.docx,.pptx,.xlsx,.pdf,.png,.jpg,.jpeg,.webp"
                    class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-slate-700 shadow-sm outline-none transition file:mr-4 file:rounded-xl file:border-0 file:bg-sky-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-sky-800 focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                    @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''"
                >
                <p class="mt-2 text-xs leading-5 text-slate-500">{{ __('ui.material_supported_formats') }}</p>
                <p x-show="fileName" x-text="'File siap diunggah: ' + fileName" class="mt-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700"></p>
            </div>

            <div>
                <label for="raw_text" class="mb-2 block text-sm font-bold text-slate-700">{{ __('ui.material_text') }}</label>
                <textarea id="raw_text" name="raw_text" rows="10" class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-slate-950 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="{{ __('ui.material_text_placeholder') }}">{{ old('raw_text') }}</textarea>
                <p class="mt-2 text-xs leading-5 text-slate-500">{{ __('ui.material_input_choice') }}</p>
            </div>

            <div x-show="uploading" style="display: none;" class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 animate-spin text-sky-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                    </svg>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-extrabold text-slate-900">Mengunggah dan membaca materi...</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">Sistem sedang menyimpan file, mengekstrak teks, lalu membuat ringkasan. PDF scan atau gambar bisa membutuhkan waktu lebih lama.</p>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                            <div class="indeterminate-loader h-full rounded-full bg-sky-500"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('materials.index') }}" class="rounded-xl border border-sky-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-sky-50" :class="{ 'pointer-events-none opacity-60': uploading }">{{ __('ui.cancel') }}</a>
                <button type="submit" class="user-primary-button px-6 py-3 disabled:cursor-wait disabled:opacity-75" :disabled="uploading">
                    <span x-show="!uploading">{{ __('ui.save_material') }}</span>
                    <span x-show="uploading" style="display: none;" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
