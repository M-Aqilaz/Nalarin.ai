<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\AiSummary;
use App\Models\Material;
use App\Services\Learning\MaterialTextExtractor;
use App\Support\ResolvesLearningUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MaterialController extends Controller
{
    use ResolvesLearningUser;

    public function index(): View
    {
        $materials = Material::query()
            ->withCount(['summaries', 'chatThreads'])
            ->latest()
            ->get();

        return view('materials.index', compact('materials'));
    }

    public function create(): View
    {
        return view('materials.create');
    }

    public function store(Request $request, MaterialTextExtractor $textExtractor): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'material_file' => ['nullable', 'file', 'max:51200', 'required_without:raw_text'],
            'raw_text' => ['nullable', 'string', 'required_without:material_file'],
        ]);

        $user = $this->resolveLearningUser();
        $file = $request->file('material_file');
        $providedText = trim((string) ($validated['raw_text'] ?? ''));
        $extracted = $file && $providedText === ''
            ? $textExtractor->extractFromUpload($file)
            : ['text' => '', 'warning' => null];
        $rawText = $providedText !== '' ? $providedText : $extracted['text'];

        if ($rawText === '') {
            return back()
                ->withInput()
                ->withErrors([
                    'material_file' => $extracted['warning'] ?? 'Materi belum mengandung teks yang bisa dipakai untuk flashcard dan kuis.',
                ]);
        }

        $storedPath = $file?->store('materials');

        $material = Material::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'original_filename' => $file?->getClientOriginalName(),
            'file_path' => $storedPath,
            'mime_type' => $file?->getMimeType(),
            'file_size' => $file?->getSize(),
            'raw_text' => $rawText,
            'status' => 'processed',
        ]);

        AiSummary::create([
            'material_id' => $material->id,
            'user_id' => $user->id,
            'title' => 'Ringkasan ' . $material->title,
            'summary_text' => $this->buildSummary($rawText),
            'model' => 'local-placeholder',
        ]);

        $redirect = redirect()
            ->route('materials.show', $material)
            ->with('status', 'Materi berhasil dibuat.');

        if ($extracted['warning']) {
            $redirect->with('warning', $extracted['warning']);
        }

        return $redirect;
    }

    public function show(Material $material): View
    {
        $material->load(['user', 'summaries', 'chatThreads.messages', 'flashcardDeck', 'quizSet']);

        return view('materials.show', compact('material'));
    }

    private function buildSummary(string $text): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? $text;
        $excerpt = Str::limit($normalized, 500, '...');

        return "Ringkasan awal dari materi ini:\n\n" . $excerpt;
    }
}
