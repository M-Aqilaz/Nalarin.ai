<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\QuizAttempt;
use App\Models\QuizSet;
use App\Services\Analytics\AnalyticsTracker;
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

    public function generate(Request $request, StudyContentGenerator $generator, AiContentGenerationLimiter $limiter, AnalyticsTracker $analytics): RedirectResponse
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
                ->withErrors(['material_id' => __('ui.quiz_material_insufficient')]);
        }

        $limiter->hit($request->user(), 'quiz');

        $quiz = $material->quizSet()->updateOrCreate(
            [],
            [
                'title' => __('ui.quiz_title_for', ['material' => $material->title]),
                'description' => __('ui.quiz_ready_description'),
                'question_count' => count($questions),
            ]
        );

        $quiz->questions()->delete();
        $quiz->questions()->createMany($questions);
        session()->forget($this->sessionKey($quiz));

        $analytics->trackFeature($request->user(), 'quiz_generate', 'Latihan Kuis', 'generated', [
            'material_id' => $material->id,
            'quiz_set_id' => $quiz->id,
            'question_count' => count($questions),
        ], $request);

        return redirect()
            ->route('feature.quiz', ['material_id' => $material->id])
            ->with('status', __('ui.quiz_created'));
    }

    public function start(QuizSet $quizSet, AnalyticsTracker $analytics): RedirectResponse
    {
        abort_unless($quizSet->material->user_id === auth()->id(), 403);
        $questionCount = $quizSet->questions()->count();
        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_set_id' => $quizSet->id,
            'material_id' => $quizSet->material_id,
            'status' => QuizAttempt::STATUS_IN_PROGRESS,
            'total_questions' => $questionCount,
            'started_at' => now(),
        ]);

        session()->put($this->sessionKey($quizSet), [
            'attempt_id' => $attempt->id,
            'current_index' => 0,
            'answers' => [],
            'completed' => false,
        ]);

        $analytics->trackFeature(auth()->user(), 'quiz_start', 'Latihan Kuis', 'started', [
            'material_id' => $quizSet->material_id,
            'quiz_set_id' => $quizSet->id,
            'quiz_attempt_id' => $attempt->id,
        ], request());

        return redirect()->route('feature.quiz', ['material_id' => $quizSet->material_id]);
    }

    public function answer(Request $request, QuizSet $quizSet, AnalyticsTracker $analytics): RedirectResponse
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
                ->withErrors(['choice' => __('ui.quiz_order_changed')]);
        }

        $attempt['answers'][$currentQuestion->id] = (int) $validated['choice'];
        $attempt['current_index'] = $currentIndex + 1;
        $attempt['completed'] = $attempt['current_index'] >= $questions->count();

        $quizAttempt = $this->resolveAttempt($quizSet, $request->user()->id, $attempt);
        $quizAttempt->answers()->updateOrCreate(
            ['quiz_question_id' => $currentQuestion->id],
            [
                'selected_choice' => (int) $validated['choice'],
                'correct_choice' => (int) $currentQuestion->correct_choice,
                'is_correct' => (int) $validated['choice'] === (int) $currentQuestion->correct_choice,
                'answered_at' => now(),
            ],
        );

        $attempt['attempt_id'] = $quizAttempt->id;

        $analytics->trackFeature($request->user(), 'quiz_answer', 'Latihan Kuis', 'answered', [
            'material_id' => $quizSet->material_id,
            'quiz_set_id' => $quizSet->id,
            'quiz_attempt_id' => $quizAttempt->id,
            'question_id' => $currentQuestion->id,
        ], $request);

        if ($attempt['completed']) {
            $this->completeAttempt($quizAttempt, $quizSet);
            $analytics->trackFeature($request->user(), 'quiz_complete', 'Latihan Kuis', 'completed', [
                'material_id' => $quizSet->material_id,
                'quiz_set_id' => $quizSet->id,
                'quiz_attempt_id' => $quizAttempt->id,
                'score' => $quizAttempt->fresh()->score,
                'total_questions' => $questions->count(),
            ], $request);
        }

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

    private function resolveAttempt(QuizSet $quizSet, int $userId, array $sessionAttempt): QuizAttempt
    {
        $attemptId = (int) ($sessionAttempt['attempt_id'] ?? 0);

        if ($attemptId > 0) {
            $attempt = QuizAttempt::query()
                ->where('user_id', $userId)
                ->where('quiz_set_id', $quizSet->id)
                ->find($attemptId);

            if ($attempt) {
                return $attempt;
            }
        }

        return QuizAttempt::create([
            'user_id' => $userId,
            'quiz_set_id' => $quizSet->id,
            'material_id' => $quizSet->material_id,
            'status' => QuizAttempt::STATUS_IN_PROGRESS,
            'total_questions' => $quizSet->questions()->count(),
            'started_at' => now(),
        ]);
    }

    private function completeAttempt(QuizAttempt $attempt, QuizSet $quizSet): void
    {
        $total = $quizSet->questions()->count();
        $score = $attempt->answers()->where('is_correct', true)->count();

        $attempt->update([
            'status' => QuizAttempt::STATUS_COMPLETED,
            'score' => $score,
            'total_questions' => $total,
            'percentage' => $total > 0 ? round(($score / $total) * 100, 2) : 0,
            'completed_at' => now(),
        ]);
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
