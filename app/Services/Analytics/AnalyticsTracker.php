<?php

namespace App\Services\Analytics;

use App\Models\FeatureEvent;
use App\Models\FeatureUsage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyticsTracker
{
    public function trackFeature(
        ?User $user,
        string $featureKey,
        string $featureName,
        string $action = 'used',
        array $metadata = [],
        ?Request $request = null,
    ): FeatureEvent {
        $normalizedKey = $this->normalizeKey($featureKey);
        $occurredAt = now();

        $event = FeatureEvent::create([
            'user_id' => $user?->id,
            'feature_key' => $normalizedKey,
            'feature_name' => $featureName,
            'action' => $action,
            'route_name' => $request?->route()?->getName(),
            'path' => $request?->path(),
            'metadata' => $metadata,
            'occurred_at' => $occurredAt,
        ]);

        FeatureUsage::firstOrCreate(
            ['feature_name' => $featureName],
            ['click_count' => 0],
        )->increment('click_count');

        return $event;
    }

    public function trackFeatureByName(?User $user, string $featureName, string $action = 'click', array $metadata = [], ?Request $request = null): FeatureEvent
    {
        return $this->trackFeature($user, $featureName, $featureName, $action, $metadata, $request);
    }

    private function normalizeKey(string $key): string
    {
        $normalized = Str::of($key)->lower()->slug('_')->toString();

        return $normalized !== '' ? $normalized : 'unknown';
    }
}
