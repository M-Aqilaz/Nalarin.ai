<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\Material;
use App\Services\Analytics\AnalyticsTracker;
use App\Services\Learning\FlashcardReviewScheduler;
use App\Services\Learning\StudyContentGenerator;
use App\Support\AiContentGenerationLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlashcardController extends Controller
{
    public function index(Request $request): View
    {
        $materials = Material::query()->where('user_id', $request->user()->id)->latest()->get(['id', 'title', 'status']);
        $selectedMaterial = $request->integer('material_id')
            ? Material::query()
                ->where('user_id', $request->user()->id)
                ->select(['id', 'user_id', 'title', 'status'])
                ->with([
                    'flashcardDeck:id,material_id,title,description,card_count',
                    'flashcardDeck.cards:id,flashcard_deck_id,front,back,example,difficulty,sort_order,review_count,streak,last_reviewed_at,next_review_at',
                ])
                ->find($request->integer('material_id'))
            : null;

        $deck = $selectedMaterial?->flashcardDeck;
        $dueCards = collect();
        $currentCard = null;

        if ($deck) {
            $deck->setRelation('cards', $deck->cards->sortBy('sort_order')->values());

            $dueCards = $deck->cards->filter(function (Flashcard $card): bool {
                return $card->next_review_at === null || $card->next_review_at->isPast();
            })->values();

            $currentCard = $dueCards->first() ?? $deck->cards->sortBy('next_review_at')->first();
        }

        return view('pages.user.flashcards.index', [
            'materials' => $materials,
            'selectedMaterial' => $selectedMaterial,
            'deck' => $deck,
            'currentCard' => $currentCard,
            'dueCount' => $dueCards->count(),
        ]);
    }

    public function generate(Request $request, StudyContentGenerator $generator, AiContentGenerationLimiter $limiter, AnalyticsTracker $analytics): RedirectResponse
    {
        $validated = $request->validate([
            'material_id' => ['required', 'exists:materials,id'],
        ]);

        $material = Material::query()->where('user_id', $request->user()->id)->findOrFail($validated['material_id']);
        $limit = $limiter->check($request->user(), 'flashcards');

        if (! $limit['allowed']) {
            return redirect()
                ->route('feature.flashcards', ['material_id' => $material->id])
                ->withErrors(['material_id' => $limit['message']]);
        }

        $existingFronts = $material->flashcardDeck?->cards()
            ->pluck('front')
            ->all() ?? [];
        $cards = $generator->generateFlashcards($material, 12, $existingFronts);

        if (count($cards) < 4) {
            return redirect()
                ->route('feature.flashcards', ['material_id' => $material->id])
                ->withErrors(['material_id' => 'Materi ini belum cukup jelas untuk dijadikan flashcard. Tambahkan materi yang lebih lengkap.']);
        }

        $limiter->hit($request->user(), 'flashcards');

        $deck = $material->flashcardDeck()->updateOrCreate(
            [],
            [
                'title' => 'Smart Flashcard: ' . $material->title,
                'description' => 'Deck belajar otomatis dari materi yang diunggah.',
                'card_count' => count($cards),
            ]
        );

        $deck->cards()->delete();
        $deck->cards()->createMany($cards);

        $analytics->trackFeature($request->user(), 'flashcards_generate', 'Smart Flashcard', 'generated', [
            'material_id' => $material->id,
            'deck_id' => $deck->id,
            'card_count' => count($cards),
        ], $request);

        return redirect()
            ->route('feature.flashcards', ['material_id' => $material->id])
            ->with('status', 'Flashcard berhasil dibuat dari materi terpilih.');
    }

    public function review(Request $request, FlashcardDeck $deck, FlashcardReviewScheduler $scheduler, AnalyticsTracker $analytics): RedirectResponse
    {
        $validated = $request->validate([
            'flashcard_id' => ['required', 'exists:flashcards,id'],
            'rating' => ['required', 'in:again,hard,good,easy'],
        ]);

        abort_unless($deck->material()->where('user_id', $request->user()->id)->exists(), 403);
        $card = $deck->cards()->findOrFail($validated['flashcard_id']);
        $scheduler->apply($card, $validated['rating']);

        $analytics->trackFeature($request->user(), 'flashcards_review', 'Smart Flashcard', 'reviewed', [
            'deck_id' => $deck->id,
            'flashcard_id' => $card->id,
            'rating' => $validated['rating'],
        ], $request);

        return redirect()
            ->route('feature.flashcards', ['material_id' => $deck->material_id])
            ->with('status', 'Progress flashcard diperbarui.');
    }
}
