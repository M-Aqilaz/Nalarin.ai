<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">Material Intake</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">Unggah Materi Baru</h2>
            <p class="mt-2 text-sm text-slate-300/80">Simpan file atau teks materi, lalu teruskan ke OCR, ringkasan, flashcard, kuis, dan AI tutor dari pipeline yang sama.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <x-nala-guide
            mood="flat"
            title="Upload yang rapi, ya"
            message="Kasih judul yang jelas dan pakai file yang mudah dibaca. Kalau hasil scan buram, Nala tetap coba bantu, tapi jangan sengaja bikin susah."
            compact
        />

        <section class="feature-hero">
            <div class="max-w-2xl">
                <p class="user-kicker text-[11px] text-cyan-100/90">One Entry Point</p>
                <p class="mt-3 text-sm text-slate-100/80">PDF, gambar, atau file modern Office bisa masuk dari halaman ini. Jika memungkinkan, teks akan diekstrak dulu lalu dipakai untuk seluruh fitur belajar.</p>
            </div>
        </section>

        <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data" class="glass-panel accent-card-cyan rounded-3xl p-8 space-y-6">
            @csrf

            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-2xl border border-cyan-400/20 bg-cyan-400/10 p-4 text-sm text-cyan-100">
                Upload PDF, gambar, DOCX, PPTX, atau XLSX. Jika file berupa scan, sistem akan mencoba OCR dengan Tesseract lalu AI merapikan hasilnya menjadi ringkasan.
                @unless (auth()->user()->isPremium())
                    Akun free dibatasi sampai {{ config('services.ocr.free_max_pages', 5) }} halaman OCR per PDF.
                @endunless
            </div>

            <div>
                <label for="title" class="mb-2 block text-sm font-medium text-slate-200">Judul Materi</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="glass-input w-full px-4 py-3" required>
            </div>

            <div>
                <label for="material_file" class="mb-2 block text-sm font-medium text-slate-200">File Materi</label>
                <input id="material_file" name="material_file" type="file" accept=".txt,.md,.markdown,.csv,.json,.xml,.html,.htm,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.odt,.odp,.ods,.rtf,.pdf,.png,.jpg,.jpeg,.webp,.tif,.tiff,.bmp" class="glass-input w-full px-4 py-3 text-slate-300">
                <p class="mt-2 text-xs text-slate-300/55">Format lama seperti .doc/.ppt/.xls belum didukung di mode ringan. Convert ke PDF, DOCX, PPTX, atau XLSX. PDF scan dan gambar butuh Poppler/Tesseract.</p>
            </div>

            <div>
                <label for="raw_text" class="mb-2 block text-sm font-medium text-slate-200">Teks Materi</label>
                <textarea id="raw_text" name="raw_text" rows="10" class="glass-input w-full px-4 py-3" placeholder="Opsional. Dipakai hanya jika tidak upload file, atau sebagai fallback jika file gagal dibaca.">{{ old('raw_text') }}</textarea>
                <p class="mt-2 text-xs text-slate-300/55">Jika file diupload, sistem akan memproses isi file terlebih dahulu sebelum memakai teks manual.</p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('materials.index') }}" class="rounded-xl border border-white/10 bg-white/[0.08] px-5 py-3 text-slate-200">Batal</a>
                <button type="submit" class="user-primary-button px-6 py-3">Simpan Materi</button>
            </div>
        </form>
    </div>
</x-app-layout>
