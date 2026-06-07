<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('billing:reset-annual-credits', function () {
    $resetCount = 0;

    User::query()
        ->where('plan', 'premium')
        ->where('plan_key', 'ultimate_yearly')
        ->where('plan_expires_at', '>', now())
        ->whereNotNull('match_credits_reset_at')
        ->where('match_credits_reset_at', '<=', now())
        ->chunkById(100, function ($users) use (&$resetCount) {
            foreach ($users as $user) {
                $nextResetAt = $user->match_credits_reset_at->copy();

                do {
                    $nextResetAt = $nextResetAt->addMonth();
                } while ($nextResetAt->lte(now()));

                $user->forceFill([
                    'match_credits' => max(0, (int) $user->match_credits_monthly_allowance),
                    'match_credits_reset_at' => $nextResetAt,
                ])->save();

                $resetCount++;
            }
        });

    $this->info("Annual credit reset completed for {$resetCount} user(s).");
})->purpose('Reset monthly match credits for active Ultimate annual users');

Schedule::command('billing:reset-annual-credits')->dailyAt('00:10');
