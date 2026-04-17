<?php

namespace App\Services\Learning;

use App\Models\Flashcard;
use Carbon\CarbonImmutable;

class FlashcardReviewScheduler
{
    public function apply(Flashcard $card, string $rating): Flashcard
    {
        $now = CarbonImmutable::now();
        $easeFactor = (float) $card->ease_factor;
        $intervalMinutes = max(0, (int) $card->interval_minutes);

        [$nextInterval, $nextEase, $nextStreak] = match ($rating) {
            'again' => [10, max(1.3, $easeFactor - 0.20), 0],
            'hard' => [max(30, (int) round(max($intervalMinutes, 30) * 1.35)), max(1.4, $easeFactor - 0.10), max(0, $card->streak)],
            'easy' => [max(240, (int) round(max($intervalMinutes, 120) * max($easeFactor, 2.2))), min(3.0, $easeFactor + 0.15), $card->streak + 1],
            default => [max(60, (int) round(max($intervalMinutes, 60) * max($easeFactor, 1.8))), min(3.0, $easeFactor + 0.05), $card->streak + 1],
        };

        if ($card->review_count === 0) {
            $nextInterval = match ($rating) {
                'again' => 10,
                'hard' => 45,
                'easy' => 24 * 60 * 3,
                default => 24 * 60,
            };
        }

        $card->forceFill([
            'review_count' => $card->review_count + 1,
            'streak' => $nextStreak,
            'interval_minutes' => $nextInterval,
            'ease_factor' => $nextEase,
            'last_reviewed_at' => $now,
            'next_review_at' => $now->addMinutes($nextInterval),
        ])->save();

        return $card->fresh();
    }
}
