<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Throwable;

class SafeBroadcast
{
    public static function event(object $event): void
    {
        try {
            broadcast($event);
        } catch (Throwable $throwable) {
            Log::warning('Realtime broadcast skipped.', [
                'event' => $event::class,
                'message' => $throwable->getMessage(),
            ]);
        }
    }
}
