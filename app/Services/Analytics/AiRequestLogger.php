<?php

namespace App\Services\Analytics;

use App\Models\AiRequest;
use Illuminate\Support\Str;
use Throwable;

class AiRequestLogger
{
    public function start(array $attributes): AiRequest
    {
        return AiRequest::create([
            'status' => AiRequest::STATUS_PROCESSING,
            'started_at' => now(),
            ...$attributes,
        ]);
    }

    public function success(AiRequest $request, array $attributes = []): AiRequest
    {
        $completedAt = now();

        $request->forceFill([
            'status' => AiRequest::STATUS_SUCCESS,
            'completed_at' => $completedAt,
            'latency_ms' => $this->latency($request, $completedAt),
            'error_message' => null,
            ...array_filter($attributes, fn ($value) => $value !== null),
        ])->save();

        return $request;
    }

    public function failed(AiRequest $request, Throwable|string $error, array $attributes = []): AiRequest
    {
        $completedAt = now();
        $message = $error instanceof Throwable ? $error->getMessage() : $error;

        $request->forceFill([
            'status' => AiRequest::STATUS_FAILED,
            'completed_at' => $completedAt,
            'latency_ms' => $this->latency($request, $completedAt),
            'error_message' => Str::limit($message, 1000, ''),
            ...array_filter($attributes, fn ($value) => $value !== null),
        ])->save();

        return $request;
    }

    public function providerFromBaseUrl(string $baseUrl): string
    {
        return str_contains($baseUrl, 'openrouter.ai') ? 'openrouter' : 'openai';
    }

    private function latency(AiRequest $request, mixed $completedAt): ?int
    {
        if (! $request->started_at) {
            return null;
        }

        return (int) $request->started_at->diffInMilliseconds($completedAt, true);
    }
}
