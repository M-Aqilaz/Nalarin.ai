<?php

namespace App\Services\Ai;

use App\Contracts\AiThreadResponder;
use App\Data\AiReplyResult;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class OpenAiThreadResponder implements AiThreadResponder
{
    public function generateReply(ChatThread $thread): AiReplyResult
    {
        $apiKey = (string) config('services.openai.api_key');
        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://openrouter.ai/api/v1'), '/');

        if ($apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY belum diisi.');
        }

        if (str_contains($baseUrl, 'openrouter.ai')) {
            return $this->generateChatCompletion($thread, $apiKey, $baseUrl);
        }

        return $this->generateResponse($thread, $apiKey, $baseUrl);
    }

    private function generateResponse(ChatThread $thread, string $apiKey, string $baseUrl): AiReplyResult
    {
        $response = $this->client($apiKey, $baseUrl)
            ->post('/responses', [
                'model' => config('services.openai.model', 'gpt-5-mini'),
                'instructions' => $this->buildInstructions($thread),
                'input' => $thread->messages
                    ->sortBy('id')
                    ->values()
                    ->map(fn ($message) => [
                        'role' => $message->role === 'system' ? 'developer' : $message->role,
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $message->content,
                            ],
                        ],
                    ])
                    ->all(),
                'max_output_tokens' => (int) config('services.openai.max_output_tokens', 800),
                'truncation' => 'auto',
            ]);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            $message = $response->json('error.message') ?: $exception->getMessage();

            throw new RuntimeException($message, previous: $exception);
        }

        $content = trim((string) ($response->json('output_text') ?? $this->extractMessageText($response->json('output', []))));

        if ($content === '') {
            throw new RuntimeException('Provider AI tidak mengembalikan teks jawaban.');
        }

        return new AiReplyResult(
            content: $content,
            inputTokens: $response->json('usage.input_tokens'),
            outputTokens: $response->json('usage.output_tokens'),
            responseId: $response->json('id'),
        );
    }

    private function generateChatCompletion(ChatThread $thread, string $apiKey, string $baseUrl): AiReplyResult
    {
        $latestImageMessageId = $this->latestImageMessageId($thread);
        $usesVision = $latestImageMessageId !== null;
        $messages = collect([
            [
                'role' => 'system',
                'content' => $this->buildInstructions($thread),
            ],
        ])->merge(
            $thread->messages
                ->sortBy('id')
                ->values()
                ->map(fn ($message) => [
                    'role' => $message->role === 'system' ? 'system' : $message->role,
                    'content' => $this->buildMessageContent($message, $latestImageMessageId),
                ])
        )->values()->all();

        if ($usesVision) {
            return $this->generateVisionChatCompletion($apiKey, $baseUrl, $messages);
        }

        return $this->sendChatCompletion(
            $apiKey,
            $baseUrl,
            (string) config('services.openai.model', 'openai/gpt-oss-120b:free'),
            $messages,
            (int) config('services.openai.timeout', 60),
        );
    }

    private function generateVisionChatCompletion(string $apiKey, string $baseUrl, array $messages): AiReplyResult
    {
        $models = $this->visionModels();
        $lastException = null;

        foreach ($models as $model) {
            if ($this->isVisionModelCoolingDown($model)) {
                continue;
            }

            try {
                return $this->sendChatCompletion(
                    $apiKey,
                    $baseUrl,
                    $model,
                    $messages,
                    (int) config('services.openai.vision_timeout', 45),
                    (array) config('services.openai.vision_provider_ignore', []),
                    true,
                );
            } catch (Throwable $exception) {
                $lastException = $exception;
                $this->cooldownVisionModel($model, $exception);
            }
        }

        foreach ($models as $model) {
            try {
                return $this->sendChatCompletion(
                    $apiKey,
                    $baseUrl,
                    $model,
                    $messages,
                    (int) config('services.openai.vision_fallback_timeout', 55),
                    (array) config('services.openai.vision_provider_ignore', []),
                    true,
                );
            } catch (Throwable $exception) {
                $lastException = $exception;
            }
        }

        throw new RuntimeException(
            'Nala belum bisa membaca gambar ini sekarang karena semua model vision sedang penuh atau lambat. Coba kirim ulang gambar yang lebih kecil, crop bagian pentingnya, atau ketik isi gambarnya.',
            previous: $lastException,
        );
    }

    private function sendChatCompletion(string $apiKey, string $baseUrl, string $model, array $messages, int $timeout, array $providerIgnore = [], bool $expectsVision = false): AiReplyResult
    {
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => (int) config('services.openai.max_output_tokens', 800),
        ];

        if ($providerIgnore !== []) {
            $payload['provider'] = [
                'ignore' => array_values($providerIgnore),
            ];
        }

        $response = $this->client($apiKey, $baseUrl)
            ->timeout($timeout)
            ->withHeaders(array_filter([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name', 'Nalarin.ai'),
            ]))
            ->post('/chat/completions', $payload);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            $message = $response->json('error.message') ?: $exception->getMessage();

            throw new RuntimeException($message, previous: $exception);
        }

        $content = trim((string) $response->json('choices.0.message.content'));

        if ($content === '') {
            throw new RuntimeException('Provider AI tidak mengembalikan teks jawaban.');
        }

        if ($expectsVision && $this->looksLikeImageBlindReply($content)) {
            throw new RuntimeException('Provider AI tidak membaca gambar pada request vision.');
        }

        return new AiReplyResult(
            content: $content,
            inputTokens: $response->json('usage.prompt_tokens'),
            outputTokens: $response->json('usage.completion_tokens'),
            responseId: $response->json('id'),
        );
    }

    private function looksLikeImageBlindReply(string $content): bool
    {
        $normalized = str($content)->lower()->toString();

        return str_contains($normalized, 'tidak bisa melihat gambar')
            || str_contains($normalized, 'tidak dapat melihat gambar')
            || str_contains($normalized, 'nala tidak bisa melihat gambar')
            || str_contains($normalized, 'i can\'t see the image')
            || str_contains($normalized, 'i cannot see the image')
            || str_contains($normalized, 'unable to view the image');
    }

    private function visionModels(): array
    {
        $models = (array) config('services.openai.vision_models', []);

        if ($models === []) {
            $models = array_filter([
                config('services.openai.vision_model'),
                config('services.openai.vision_fallback_model'),
            ]);
        }

        $models = array_values(array_unique(array_filter(array_map(fn ($model) => trim((string) $model), $models))));

        return $this->rotateVisionModels($models);
    }

    private function rotateVisionModels(array $models): array
    {
        if (count($models) <= 1) {
            return $models;
        }

        $key = 'ai:vision-model-rotation-index';
        $index = (int) Cache::get($key, 0);

        Cache::put($key, ($index + 1) % count($models), now()->addDay());

        return array_merge(array_slice($models, $index), array_slice($models, 0, $index));
    }

    private function isVisionModelCoolingDown(string $model): bool
    {
        return Cache::has($this->visionCooldownKey($model));
    }

    private function cooldownVisionModel(string $model, Throwable $exception): void
    {
        $seconds = (int) config('services.openai.vision_cooldown_seconds', 180);

        if ($seconds <= 0) {
            return;
        }

        Cache::put($this->visionCooldownKey($model), str($exception->getMessage())->limit(240)->toString(), now()->addSeconds($seconds));
    }

    private function visionCooldownKey(string $model): string
    {
        return 'ai:vision-model-cooldown:'.sha1($model);
    }

    private function latestImageMessageId(ChatThread $thread): ?int
    {
        $message = $thread->messages
            ->sortByDesc('id')
            ->first();

        if (! $message || $message->attachments->where('kind', 'image')->isEmpty()) {
            return null;
        }

        return $message->id;
    }

    private function buildMessageContent(ChatMessage $message, ?int $latestImageMessageId): string|array
    {
        $imageAttachments = $message->attachments->where('kind', 'image')->values();

        if ($imageAttachments->isEmpty()) {
            return $message->content;
        }

        if ($latestImageMessageId !== $message->id) {
            return trim($message->content."\n\n[Gambar pernah dilampirkan pada pesan ini, tetapi tidak dikirim ulang agar konteks tetap ringan.]");
        }

        $content = [
            [
                'type' => 'text',
                'text' => $message->content,
            ],
        ];

        foreach ($imageAttachments as $attachment) {
            $binary = Storage::disk($attachment->disk)->get($attachment->path);

            if ($binary === null || $binary === false) {
                continue;
            }

            $content[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => 'data:'.$attachment->mime_type.';base64,'.base64_encode($binary),
                ],
            ];
        }

        return $content;
    }

    private function client(string $apiKey, string $baseUrl): \Illuminate\Http\Client\PendingRequest
    {
        return Http::baseUrl($baseUrl)
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('services.openai.timeout', 60));
    }

    private function buildInstructions(ChatThread $thread): string
    {
        $materialTitle = $thread->material?->title;

        return trim(implode("\n", array_filter([
            'Anda adalah Nala, maskot sekaligus tutor belajar AI untuk Nalarin.ai.',
            'Persona Nala: soft tsundere, sedikit jutek dan lucu, tetapi tetap suportif, sopan, dan fokus membantu user belajar.',
            'Gunakan sudut pandang orang pertama sebagai Nala. Boleh menyelipkan gaya seperti "hmph", "jangan malas", atau "bukan berarti Nala khawatir", tetapi jangan berlebihan.',
            'Jawab dalam Bahasa Indonesia yang jelas, ringkas, mudah diikuti, dan fokus pada bantuan belajar.',
            'Jangan merendahkan user, jangan flirting berlebihan, jangan masuk roleplay dewasa, dan jangan mengorbankan kejelasan materi demi persona.',
            'Kalau konteks materi kurang lengkap, katakan secara jujur dan minta klarifikasi.',
            $materialTitle ? "Materi yang sedang dipelajari: {$materialTitle}." : null,
        ])));
    }

    private function extractMessageText(array $output): string
    {
        $segments = [];

        foreach ($output as $item) {
            if (($item['type'] ?? null) !== 'message') {
                continue;
            }

            foreach ($item['content'] ?? [] as $content) {
                if (($content['type'] ?? null) === 'output_text' && isset($content['text'])) {
                    $segments[] = $content['text'];
                }
            }
        }

        return implode("\n\n", $segments);
    }
}
