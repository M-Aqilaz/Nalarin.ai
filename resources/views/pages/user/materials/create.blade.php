<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Unggah Materi Baru</h2>
            <p class="text-sm text-gray-400 mt-1">Versi dasar untuk menyimpan file atau teks materi.</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data" class="glass-panel rounded-3xl border border-white/5 p-8 space-y-6">
            @csrf

            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-2xl border border-blue-500/20 bg-blue-500/10 p-4 text-sm text-blue-100">
                Upload PDF, gambar, DOCX, PPTX, atau XLSX. Jika file berupa scan, sistem akan mencoba OCR dengan Tesseract lalu AI merapikan hasilnya menjadi ringkasan.
                @unless (auth()->user()->isPremium())
                    Akun free dibatasi sampai {{ config('services.ocr.free_max_pages', 5) }} halaman OCR per PDF.
                @endunless
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Judul Materi</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required>
            </div>

            <div>
                <label for="material_file" class="block text-sm font-medium text-gray-300 mb-2">File Materi</label>
                <input id="material_file" name="material_file" type="file" accept=".txt,.md,.markdown,.csv,.json,.xml,.html,.htm,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.odt,.odp,.ods,.rtf,.pdf,.png,.jpg,.jpeg,.webp,.tif,.tiff,.bmp" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-gray-300">
                <p class="mt-2 text-xs text-gray-500">Format lama seperti .doc/.ppt/.xls belum didukung di mode ringan. Convert ke PDF, DOCX, PPTX, atau XLSX. PDF scan dan gambar butuh Poppler/Tesseract.</p>
            </div>

            <div>
                <label for="raw_text" class="block text-sm font-medium text-gray-300 mb-2">Teks Materi</label>
                <textarea id="raw_text" name="raw_text" rows="10" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" placeholder="Opsional. Dipakai hanya jika tidak upload file, atau sebagai fallback jika file gagal dibaca.">{{ old('raw_text') }}</textarea>
                <p class="mt-2 text-xs text-gray-500">Jika file diupload, sistem akan memproses isi file terlebih dahulu sebelum memakai teks manual.</p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('materials.index') }}" class="px-5 py-3 rounded-xl bg-white/5 text-gray-300">Batal</a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-purple-600 text-white font-medium">Simpan Materi</button>
            </div>
        </form>
    </div>
</x-app-layout>
