<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-sky-700">Material Intake</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">Unggah Materi Baru</h2>
            <p class="mt-2 text-sm text-slate-700">Simpan file atau teks materi, lalu teruskan ke OCR, ringkasan, flashcard, kuis, dan AI tutor dari pipeline yang sama.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <x-nala-guide
            mood="flat"
            title="Upload yang rapi, ya"
            message="Kasih judul yang jelas dan pakai file yang mudah dibaca. Kalau hasil scan buram, Nala tetap coba bantu, tapi jangan sengaja bikin susah."
            compact
        />

        <section class="rounded-[1.75rem] border border-sky-200 bg-white/88 p-5 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
            <div class="max-w-2xl">
                <p class="user-kicker text-[11px] text-sky-700">One Entry Point</p>
                <p class="mt-3 text-sm leading-6 text-slate-700">PDF, gambar, atau file modern Office bisa masuk dari halaman ini. Jika memungkinkan, teks akan diekstrak dulu lalu dipakai untuk seluruh fitur belajar.</p>
            </div>
        </section>

        <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-[1.75rem] border border-sky-200 bg-white/90 p-8 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
            @csrf

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-6 text-slate-700">
                Unggah PDF, gambar, DOCX, PPTX, atau XLSX. Jika berkas berupa scan, sistem akan mencoba OCR dengan Tesseract lalu AI merapikan hasilnya menjadi ringkasan.
                @unless (auth()->user()->isPremium())
                    Akun free dibatasi sampai {{ config('services.ocr.free_max_pages', 5) }} halaman OCR per PDF.
                @endunless
            </div>

            <div>
                <label for="title" class="mb-2 block text-sm font-bold text-slate-700">Judul Materi</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-slate-950 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" required>
            </div>

            <div>
                <label for="material_file" class="mb-2 block text-sm font-bold text-slate-700">File Materi</label>
                <input id="material_file" name="material_file" type="file" accept=".txt,.md,.markdown,.csv,.json,.xml,.html,.htm,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.odt,.odp,.ods,.rtf,.pdf,.png,.jpg,.jpeg,.webp,.tif,.tiff,.bmp" class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-slate-700 shadow-sm outline-none transition file:mr-4 file:rounded-xl file:border-0 file:bg-sky-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-sky-800 focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                <p class="mt-2 text-xs leading-5 text-slate-500">Format lama seperti .doc/.ppt/.xls belum didukung di mode ringan. Convert ke PDF, DOCX, PPTX, atau XLSX. PDF scan dan gambar butuh Poppler/Tesseract.</p>
            </div>

            <div>
                <label for="raw_text" class="mb-2 block text-sm font-bold text-slate-700">Teks Materi</label>
                <textarea id="raw_text" name="raw_text" rows="10" class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-slate-950 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Opsional. Dipakai hanya jika tidak mengunggah berkas, atau sebagai fallback jika berkas gagal dibaca.">{{ old('raw_text') }}</textarea>
                <p class="mt-2 text-xs leading-5 text-slate-500">Jika berkas diunggah, sistem akan memproses isi berkas terlebih dahulu sebelum memakai teks manual.</p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('materials.index') }}" class="rounded-xl border border-sky-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-sky-50">Batal</a>
                <button type="submit" class="user-primary-button px-6 py-3">Simpan Materi</button>
            </div>
        </form>
    </div>
</x-app-layout>
