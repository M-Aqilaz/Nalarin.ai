<?php

namespace App\Services\Learning;

use App\Models\MatchQueueEntry;
use App\Models\StudyMatch;
use App\Models\User;
use Carbon\CarbonImmutable;

class StudyMatchingService
{
    public const ROULETTE_TOPIC_LABEL = 'Study Roulette';

    public function enqueue(User $user, array $payload): array
    {
        if (! $user->studyProfile?->is_matchmaking_enabled) {
            return ['queue' => null, 'match' => null, 'error' => 'Aktifkan profil study matching terlebih dahulu.'];
        }

        if ($user->match_credits < 1) {
            return ['queue' => null, 'match' => null, 'error' => 'Kuota study matching sudah habis.'];
        }

        $this->expireOldEntries();

        $existing = MatchQueueEntry::query()
            ->where('user_id', $user->id)
            ->where('status', 'waiting')
            ->latest()
            ->first();

        if ($existing) {
            return ['queue' => $existing, 'match' => $this->findMatchFor($user), 'error' => null];
        }

        $candidate = MatchQueueEntry::query()
            ->with('user.studyProfile')
            ->where('status', 'waiting')
            ->where('selected_topic', $payload['selected_topic'])
            ->where('user_id', '!=', $user->id)
            ->whereHas('user', fn ($query) => $query->where('match_credits', '>', 0))
            ->where(function ($query) use ($user) {
                $query->whereNotIn('user_id', $user->blockedUsers()->pluck('blocked_user_id'))
                    ->whereNotIn('user_id', $user->blockedByUsers()->pluck('user_id'));
            })
            ->oldest()
            ->first();

        if ($candidate) {
            $candidate->update(['status' => 'matched']);

            $match = StudyMatch::create([
                'user_one_id' => $candidate->user_id,
                'user_two_id' => $user->id,
                'topic' => $payload['selected_topic'],
                'status' => 'active',
                'matched_at' => now(),
            ]);

            $user->decrement('match_credits');
            $candidate->user->decrement('match_credits');

            return ['queue' => null, 'match' => $match, 'error' => null];
        }

        $queue = MatchQueueEntry::create([
            'user_id' => $user->id,
            'selected_topic' => $payload['selected_topic'],
            'preferred_level' => $payload['preferred_level'] ?? null,
            'preferred_session_type' => $payload['preferred_session_type'] ?? null,
            'status' => 'waiting',
            'expires_at' => CarbonImmutable::now()->addMinutes(20),
        ]);

        return ['queue' => $queue, 'match' => null, 'error' => null];
    }

    public function enqueueRoulette(User $user): array
    {
        if (! $user->studyProfile?->is_matchmaking_enabled) {
            return ['queue' => null, 'match' => null, 'error' => 'Aktifkan profil study matching terlebih dahulu.'];
        }

        if ($user->match_credits < 1) {
            return ['queue' => null, 'match' => null, 'error' => 'Kuota study matching sudah habis.'];
        }

        $this->expireOldEntries();

        $existing = MatchQueueEntry::query()
            ->where('user_id', $user->id)
            ->where('status', 'waiting')
            ->where('selected_topic', MatchQueueEntry::ROULETTE_TOPIC)
            ->latest()
            ->first();

        if ($existing) {
            return ['queue' => $existing, 'match' => $this->findMatchFor($user), 'error' => null];
        }

        $candidate = MatchQueueEntry::query()
            ->with('user.studyProfile')
            ->where('status', 'waiting')
            ->where('selected_topic', MatchQueueEntry::ROULETTE_TOPIC)
            ->where('user_id', '!=', $user->id)
            ->whereHas('user', fn ($query) => $query->where('match_credits', '>', 0))
            ->where(function ($query) use ($user) {
                $query->whereNotIn('user_id', $user->blockedUsers()->pluck('blocked_user_id'))
                    ->whereNotIn('user_id', $user->blockedByUsers()->pluck('user_id'));
            })
            ->oldest()
            ->first();

        if ($candidate) {
            $candidate->update(['status' => 'matched']);

            $match = StudyMatch::create([
                'user_one_id' => $candidate->user_id,
                'user_two_id' => $user->id,
                'topic' => self::ROULETTE_TOPIC_LABEL,
                'status' => 'active',
                'matched_at' => now(),
            ]);

            $user->decrement('match_credits');
            $candidate->user->decrement('match_credits');

            return ['queue' => null, 'match' => $match, 'error' => null];
        }

        $queue = MatchQueueEntry::create([
            'user_id' => $user->id,
            'selected_topic' => MatchQueueEntry::ROULETTE_TOPIC,
            'preferred_level' => null,
            'preferred_session_type' => null,
            'status' => 'waiting',
            'expires_at' => CarbonImmutable::now()->addMinutes(20),
        ]);

        return ['queue' => $queue, 'match' => null, 'error' => null];
    }

    public function cancel(User $user): void
    {
        MatchQueueEntry::query()
            ->where('user_id', $user->id)
            ->where('status', 'waiting')
            ->update(['status' => 'cancelled']);
    }

    public function cancelRoulette(User $user): void
    {
        MatchQueueEntry::query()
            ->where('user_id', $user->id)
            ->where('status', 'waiting')
            ->where('selected_topic', MatchQueueEntry::ROULETTE_TOPIC)
            ->update(['status' => 'cancelled']);
    }

    public function findMatchFor(User $user): ?StudyMatch
    {
        return StudyMatch::query()
            ->where('status', 'active')
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->latest('matched_at')
            ->first();
    }

    public function findRouletteMatchFor(User $user): ?StudyMatch
    {
        return StudyMatch::query()
            ->where('status', 'active')
            ->where('topic', self::ROULETTE_TOPIC_LABEL)
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->latest('matched_at')
            ->first();
    }

    private function expireOldEntries(): void
    {
        MatchQueueEntry::query()
            ->where('status', 'waiting')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }
}
