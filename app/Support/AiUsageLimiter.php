<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class AiUsageLimiter
{
    public function check(User $user): array
    {
        $limits = $this->limitsFor($user);

        if (RateLimiter::tooManyAttempts($this->minuteKey($user), $limits['per_minute'])) {
            return $this->denied(
                'Terlalu banyak pesan AI dalam waktu singkat. Tunggu sebentar lalu coba lagi.',
                RateLimiter::availableIn($this->minuteKey($user))
            );
        }

        if ($limits['per_day'] !== null && RateLimiter::tooManyAttempts($this->dailyKey($user), $limits['per_day'])) {
            return $this->denied(
                'Limit AI untuk akun free hari ini sudah habis. Upgrade ke Pro untuk akses lebih banyak.',
                RateLimiter::availableIn($this->dailyKey($user))
            );
        }

        return [
            'allowed' => true,
            'message' => null,
            'retry_after' => 0,
            'limits' => $limits,
        ];
    }

    public function hit(User $user): void
    {
        $limits = $this->limitsFor($user);

        RateLimiter::hit($this->minuteKey($user), 60);

        if ($limits['per_day'] !== null) {
            RateLimiter::hit($this->dailyKey($user), (int) now()->diffInSeconds(now()->endOfDay()));
        }
    }

    private function limitsFor(User $user): array
    {
        if ($user->isPremium()) {
            return [
                'per_minute' => (int) config('services.openai.limits.premium_per_minute', 20),
                'per_day' => null,
            ];
        }

        return [
            'per_minute' => (int) config('services.openai.limits.free_per_minute', 4),
            'per_day' => (int) config('services.openai.limits.free_per_day', 10),
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

    private function minuteKey(User $user): string
    {
        return "ai-chat:minute:user:{$user->id}";
    }

    private function dailyKey(User $user): string
    {
        return 'ai-chat:day:user:'.$user->id.':'.now()->toDateString();
    }
}
