<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class TypingStateStore
{
    private const TTL_SECONDS = 8;

    public function touch(string $scope, int $channelId, int $userId, string $userName): void
    {
        $key = $this->key($scope, $channelId);
        $state = $this->read($scope, $channelId);
        $state[(string) $userId] = [
            'user_id' => $userId,
            'user_name' => $userName,
            'expires_at' => now()->addSeconds(self::TTL_SECONDS)->timestamp,
        ];

        Cache::put($key, $state, now()->addSeconds(self::TTL_SECONDS));
    }

    public function active(string $scope, int $channelId, ?int $excludeUserId = null): array
    {
        $state = $this->read($scope, $channelId);

        if ($excludeUserId !== null) {
            unset($state[(string) $excludeUserId]);
        }

        return array_values(array_map(
            fn (array $entry) => [
                'user_id' => $entry['user_id'],
                'user_name' => $entry['user_name'],
            ],
            $state
        ));
    }

    private function read(string $scope, int $channelId): array
    {
        $key = $this->key($scope, $channelId);
        $state = Cache::get($key, []);
        $now = now()->timestamp;

        $filtered = collect($state)
            ->filter(fn ($entry) => ($entry['expires_at'] ?? 0) > $now)
            ->all();

        if ($filtered !== $state) {
            if ($filtered === []) {
                Cache::forget($key);
            } else {
                Cache::put($key, $filtered, now()->addSeconds(self::TTL_SECONDS));
            }
        }

        return $filtered;
    }

    private function key(string $scope, int $channelId): string
    {
        return "typing_state:{$scope}:{$channelId}";
    }
}
