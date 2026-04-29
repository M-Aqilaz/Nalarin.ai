<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\QuizSet;
use App\Services\Learning\StudyContentGenerator;
use App\Support\AiContentGenerationLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(Request $request): View
    {
        $materials = Material::query()->where('user_id', $request->user()->id)->latest()->get(['id', 'title', 'status']);
        $selectedMaterial = $request->integer('material_id')
            ? Material::query()->where('user_id', $request->user()->id)->with(['quizSet.questions'])->find($request->integer('material_id'))
            : null;
        $quiz = $selectedMaterial?->quizSet?->load('questions');
        $attempt = $quiz ? session($this->sessionKey($quiz)) : null;
        $currentQuestion = null;
        $results = null;

        if ($quiz && is_array($attempt)) {
            if (($attempt['completed'] ?? false) === true) {
                $results = $this->buildResults($quiz, $attempt);
            } else {
                $currentQuestion = $quiz->questions->get((int) ($attempt['current_index'] ?? 0));
            }
        }

        return view('pages.user.quizzes.index', [
            'materials' => $materials,
            'selectedMaterial' => $selectedMaterial,
            'quiz' => $quiz,
            'attempt' => $attempt,
            'currentQuestion' => $currentQuestion,
            'results' => $results,
        ]);
    }

    public function generate(Request $request, StudyContentGenerator $generator, AiContentGenerationLimiter $limiter): RedirectResponse
    {
        $validated = $request->validate([
            'material_id' => ['required', 'exists:materials,id'],
        ]);

        $material = Material::query()->where('user_id', $request->user()->id)->findOrFail($validated['material_id']);
        $limit = $limiter->check($request->user(), 'quiz');

        if (! $limit['allowed']) {
            return redirect()
                ->route('feature.quiz', ['material_id' => $material->id])
                ->withErrors(['material_id' => $limit['message']]);
        }

        $existingPrompts = $material->quizSet?->questions()
            ->pluck('prompt')
            ->all() ?? [];
        $questions = $generator->generateQuiz($material, 10, $existingPrompts);

        if (count($questions) < 4) {
            return redirect()
                ->route('feature.quiz', ['material_id' => $material->id])
                ->withErrors(['material_id' => 'Materi ini belum cukup kuat untuk dibuat kuis. Tambahkan materi yang lebih lengkap.']);
        }

        $limiter->hit($request->user(), 'quiz');

        $quiz = $material->quizSet()->updateOrCreate(
            [],
            [
                'title' => 'Latihan Kuis: ' . $material->title,
                'description' => 'Soal pilihan ganda otomatis dari materi belajar.',
                'question_count' => count($questions),
            ]
        );

        $quiz->questions()->delete();
        $quiz->questions()->createMany($questions);
        session()->forget($this->sessionKey($quiz));

        return redirect()
            ->route('feature.quiz', ['material_id' => $material->id])
            ->with('status', 'Kuis berhasil dibuat dari materi terpilih.');
    }

    public function start(QuizSet $quizSet): RedirectResponse
    {
        abort_unless($quizSet->material->user_id === auth()->id(), 403);
        session()->put($this->sessionKey($quizSet), [
            'current_index' => 0,
            'answers' => [],
            'completed' => false,
        ]);

        return redirect()->route('feature.quiz', ['material_id' => $quizSet->material_id]);
    }

    public function answer(Request $request, QuizSet $quizSet): RedirectResponse
    {
        abort_unless($quizSet->material->user_id === $request->user()->id, 403);
        $validated = $request->validate([
            'question_id' => ['required', 'exists:quiz_questions,id'],
            'choice' => ['required', 'integer', 'between:0,3'],
        ]);

        $attempt = session($this->sessionKey($quizSet), [
            'current_index' => 0,
            'answers' => [],
            'completed' => false,
        ]);

        $questions = $quizSet->questions()->orderBy('sort_order')->get()->values();
        $currentIndex = (int) ($attempt['current_index'] ?? 0);
        $currentQuestion = $questions->get($currentIndex);

        if (! $currentQuestion || $currentQuestion->id !== (int) $validated['question_id']) {
            return redirect()
                ->route('feature.quiz', ['material_id' => $quizSet->material_id])
                ->withErrors(['choice' => 'Urutan kuis berubah. Mulai ulang kuis terlebih dahulu.']);
        }

        $attempt['answers'][$currentQuestion->id] = (int) $validated['choice'];
        $attempt['current_index'] = $currentIndex + 1;
        $attempt['completed'] = $attempt['current_index'] >= $questions->count();

        session()->put($this->sessionKey($quizSet), $attempt);

        return redirect()->route('feature.quiz', ['material_id' => $quizSet->material_id]);
    }

    public function reset(QuizSet $quizSet): RedirectResponse
    {
        abort_unless($quizSet->material->user_id === auth()->id(), 403);
        session()->forget($this->sessionKey($quizSet));

        return redirect()->route('feature.quiz', ['material_id' => $quizSet->material_id]);
    }

    private function sessionKey(QuizSet $quizSet): string
    {
        return 'quiz_attempts.' . $quizSet->id;
    }

    private function buildResults(QuizSet $quizSet, array $attempt): array
    {
        $answers = $attempt['answers'] ?? [];
        $questions = $quizSet->questions()->orderBy('sort_order')->get();

        $items = $questions->map(function ($question) use ($answers): array {
            $selected = $answers[$question->id] ?? null;
            $correctIndex = (int) $question->correct_choice;
            $choices = $question->choices ?? [];

            return [
                'prompt' => $question->prompt,
                'selected' => $selected !== null ? ($choices[$selected] ?? null) : null,
                'correct' => $choices[$correctIndex] ?? null,
                'is_correct' => $selected === $correctIndex,
                'explanation' => $question->explanation,
            ];
        })->values();

        $score = $items->where('is_correct', true)->count();

        return [
            'score' => $score,
            'total' => $items->count(),
            'items' => $items,
        ];
    }
}
