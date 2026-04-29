<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\AiSummary;
use App\Models\Material;
use App\Services\Learning\AiMaterialCleaner;
use App\Services\Learning\MaterialTextExtractor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function index(): View
    {
        $materials = Material::query()
            ->where('user_id', auth()->id())
            ->withCount(['summaries', 'chatThreads'])
            ->latest()
            ->get();

        return view('pages.user.materials.index', compact('materials'));
    }

    public function create(): View
    {
        return view('pages.user.materials.create');
    }

    public function store(Request $request, MaterialTextExtractor $textExtractor, AiMaterialCleaner $aiMaterialCleaner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'material_file' => ['nullable', 'file', 'max:51200', 'required_without:raw_text'],
            'raw_text' => ['nullable', 'string', 'required_without:material_file'],
        ]);

        $user = $request->user();
        $file = $request->file('material_file');
        $providedText = trim((string) ($validated['raw_text'] ?? ''));
        $maxOcrPages = $user->isPremium()
            ? (int) config('services.ocr.premium_max_pages', 50)
            : (int) config('services.ocr.free_max_pages', 5);
        $extracted = $file
            ? $textExtractor->extractFromUpload($file, $maxOcrPages)
            : ['text' => '', 'warning' => null, 'used_ocr' => false, 'engine' => null];
        $fileText = trim((string) $extracted['text']);
        $usedManualFallback = $file && $fileText === '' && $providedText !== '';
        $rawText = $fileText !== '' ? $fileText : $providedText;

        if ($rawText === '') {
            return back()
                ->withInput()
                ->withErrors([
                    'material_file' => $extracted['warning'] ?? 'Materi belum mengandung teks yang bisa dipakai untuk flashcard dan kuis.',
                ]);
        }

        $aiCleaned = $file && $fileText !== ''
            ? $aiMaterialCleaner->clean($validated['title'], $rawText)
            : null;
        $rawText = $aiCleaned['text'] ?? $rawText;
        $storedPath = $file?->store('materials');
        $ocrStatus = $extracted['used_ocr'] ? 'completed' : 'not_required';

        $material = Material::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'original_filename' => $file?->getClientOriginalName(),
            'file_path' => $storedPath,
            'mime_type' => $file?->getMimeType(),
            'file_size' => $file?->getSize(),
            'raw_text' => $rawText,
            'status' => 'processed',
            'ocr_status' => $ocrStatus,
            'ocr_engine' => $extracted['engine'],
            'ocr_warning' => $usedManualFallback
                ? 'File belum bisa dibaca otomatis, jadi sistem memakai teks manual yang ditempel sebagai fallback.'
                : $extracted['warning'],
            'ocr_completed_at' => $extracted['used_ocr'] ? now() : null,
        ]);

        $aiSummary = $aiMaterialCleaner->summarize($material->title, $rawText);

        $summary = AiSummary::create([
            'material_id' => $material->id,
            'user_id' => $user->id,
            'title' => 'Ringkasan ' . $material->title,
            'summary_text' => $aiSummary['text'] ?? $this->buildSummary($rawText),
            'model' => $aiSummary['model'] ?? ($aiCleaned['model'] ?? 'local-placeholder'),
        ]);

        $redirect = redirect()
            ->route('summaries.show', $summary)
            ->with('status', 'Materi berhasil discan dan ringkasan sudah dibuat.');

        if ($usedManualFallback || $extracted['warning']) {
            $redirect->with('warning', $usedManualFallback
                ? 'File belum bisa dibaca otomatis, jadi sistem memakai teks manual yang ditempel sebagai fallback.'
                : $extracted['warning']);
        }

        return $redirect;
    }

    public function show(Material $material): View
    {
        abort_unless($material->user_id === auth()->id(), 403);

        $material->load(['user', 'summaries', 'chatThreads.messages', 'flashcardDeck', 'quizSet']);

        return view('pages.user.materials.show', compact('material'));
    }

    private function buildSummary(string $text): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? $text;
        $excerpt = Str::limit($normalized, 500, '...');

        return "Ringkasan awal dari materi ini:\n\n" . $excerpt;
    }
}
