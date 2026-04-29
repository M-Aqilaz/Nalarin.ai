<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class AiContentGenerationLimiter
{
    public function check(User $user, string $feature): array
    {
        $limits = $this->limitsFor($user);

        if (RateLimiter::tooManyAttempts($this->minuteKey($user, $feature), $limits['per_minute'])) {
            return $this->denied(
                'Terlalu sering generate konten AI. Tunggu sebentar lalu coba lagi.',
                RateLimiter::availableIn($this->minuteKey($user, $feature))
            );
        }

        if ($limits['per_day'] !== null && RateLimiter::tooManyAttempts($this->dailyKey($user), $limits['per_day'])) {
            return $this->denied(
                'Limit generate AI untuk akun free hari ini sudah habis. Upgrade ke Pro untuk generate lebih banyak.',
                RateLimiter::availableIn($this->dailyKey($user))
            );
        }

        return ['allowed' => true, 'message' => null, 'retry_after' => 0];
    }

    public function hit(User $user, string $feature): void
    {
        $limits = $this->limitsFor($user);

        RateLimiter::hit($this->minuteKey($user, $feature), 60);

        if ($limits['per_day'] !== null) {
            RateLimiter::hit($this->dailyKey($user), (int) now()->diffInSeconds(now()->endOfDay()));
        }
    }

    private function limitsFor(User $user): array
    {
        if ($user->isPremium()) {
            return [
                'per_minute' => (int) config('services.openai.limits.content_premium_per_minute', 10),
                'per_day' => null,
            ];
        }

        return [
            'per_minute' => (int) config('services.openai.limits.content_free_per_minute', 2),
            'per_day' => (int) config('services.openai.limits.content_free_per_day', 6),
        ];
    }

    private function denied(string $message, int $retryAfter): array
    {
        return [
            'allowed' => false,
            'message' => $message,
            'retry_after' => $retryAfter,
        ];
    }

    private function minuteKey(User $user, string $feature): string
    {
        return "ai-content:minute:{$feature}:user:{$user->id}";
    }

    private function dailyKey(User $user): string
    {
        return 'ai-content:day:user:'.$user->id.':'.now()->toDateString();
    }
}
