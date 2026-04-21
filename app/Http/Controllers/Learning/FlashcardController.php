<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\Material;
use App\Services\Learning\FlashcardReviewScheduler;
use App\Services\Learning\StudyContentGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlashcardController extends Controller
{
    public function index(Request $request): View
    {
        $materials = Material::query()->where('user_id', $request->user()->id)->latest()->get(['id', 'title', 'status']);
        $selectedMaterial = $request->integer('material_id')
            ? Material::query()->where('user_id', $request->user()->id)->with(['flashcardDeck.cards'])->find($request->integer('material_id'))
            : null;

        $deck = $selectedMaterial?->flashcardDeck?->load('cards');
        $dueCards = collect();
        $currentCard = null;

        if ($deck) {
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

    public function generate(Request $request, StudyContentGenerator $generator): RedirectResponse
    {
        $validated = $request->validate([
            'material_id' => ['required', 'exists:materials,id'],
        ]);

        $material = Material::query()->where('user_id', $request->user()->id)->findOrFail($validated['material_id']);
        $cards = $generator->generateFlashcards($material);

        if (count($cards) < 4) {
            return redirect()
                ->route('feature.flashcards', ['material_id' => $material->id])
                ->withErrors(['material_id' => 'Materi ini belum cukup jelas untuk dijadikan flashcard. Tambahkan materi yang lebih lengkap.']);
        }

        $deck = $material->flashcardDeck()->updateOrCreate(
            [],
            [
                'title' => 'Smart Flashcards: ' . $material->title,
                'description' => 'Deck belajar otomatis dari materi yang diunggah.',
                'card_count' => count($cards),
            ]
        );

        $deck->cards()->delete();
        $deck->cards()->createMany($cards);

        return redirect()
            ->route('feature.flashcards', ['material_id' => $material->id])
            ->with('status', 'Flashcards berhasil dibuat dari materi terpilih.');
    }

    public function review(Request $request, FlashcardDeck $deck, FlashcardReviewScheduler $scheduler): RedirectResponse
    {
        $validated = $request->validate([
            'flashcard_id' => ['required', 'exists:flashcards,id'],
            'rating' => ['required', 'in:again,hard,good,easy'],
        ]);

        abort_unless($deck->material->user_id === $request->user()->id, 403);
        $card = $deck->cards()->findOrFail($validated['flashcard_id']);
        $scheduler->apply($card, $validated['rating']);

        return redirect()
            ->route('feature.flashcards', ['material_id' => $deck->material_id])
            ->with('status', 'Progress flashcard diperbarui.');
    }
}
