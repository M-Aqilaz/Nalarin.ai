<?php

namespace App\Services\Learning;

use App\Data\AiReplyResult;
use App\Models\User;
use App\Services\Analytics\AiRequestLogger;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class OpenRouterMaterialReader
{
    public function __construct(private AiRequestLogger $aiRequests) {}

    public function read(UploadedFile $file, string $title, ?User $user = null): ?array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'pdf') {
            return $this->readPdf($file, $title, $user);
        }

        if ($this->isImage($extension)) {
            return $this->readImage($file, $title, $user);
        }

        return null;
    }

    public function supports(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return $extension === 'pdf' || $this->isImage($extension);
    }

    private function readPdf(UploadedFile $file, string $title, ?User $user): ?array
    {
        $models = $this->pdfModels();
        $messages = [
            [
                'role' => 'system',
                'content' => 'Kamu adalah pembaca dokumen untuk materi belajar. Ekstrak isi PDF menjadi teks belajar bersih dalam Bahasa Indonesia. Jangan membuat fakta baru. Pertahankan istilah penting, rumus, angka, tabel penting, heading, dan daftar. Jawab hanya teks hasil pembacaan dokumen.',
            ],
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => "Judul materi: {$title}\n\nBaca PDF ini dan keluarkan isi materinya sebagai teks yang rapi.",
                    ],
                    [
                        'type' => 'file',
                        'file' => [
                            'filename' => $file->getClientOriginalName() ?: 'material.pdf',
                            'file_data' => $this->dataUrl($file, 'application/pdf'),
                        ],
                    ],
                ],
            ],
        ];

        return $this->attemptModels(
            models: $models,
            messages: $messages,
            feature: 'material_pdf_reader',
            engine: 'openrouter-pdf',
            user: $user,
            plugins: [
                [
                    'id' => 'file-parser',
                    'pdf' => [
                        'engine' => (string) config('services.openai.pdf_engine', 'cloudflare-ai'),
                    ],
                ],
            ],
        );
    }

    private function readImage(UploadedFile $file, string $title, ?User $user): ?array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'Kamu adalah OCR visual untuk materi belajar. Baca semua teks, rumus, tabel, dan poin penting dari gambar. Jangan membuat fakta baru. Jika ada bagian tidak terbaca, tandai secara singkat. Jawab hanya teks materi yang berhasil dibaca dalam Bahasa Indonesia.',
            ],
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => "Judul materi: {$title}\n\nBaca gambar materi ini dan ubah menjadi teks belajar yang rapi.",
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => $this->dataUrl($file, $file->getMimeType() ?: $file->getClientMimeType() ?: 'image/png'),
                        ],
                    ],
                ],
            ],
        ];

        return $this->attemptModels(
            models: $this->visionModels(),
            messages: $messages,
            feature: 'material_image_reader',
            engine: 'openrouter-vision',
            user: $user,
            providerIgnore: (array) config('services.openai.vision_provider_ignore', []),
        );
    }

    private function attemptModels(array $models, array $messages, string $feature, string $engine, ?User $user, array $plugins = [], array $providerIgnore = []): ?array
    {
        $apiKey = (string) config('services.openai.api_key');
        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://openrouter.ai/api/v1'), '/');

        if ($apiKey === '' || ! str_contains($baseUrl, 'openrouter.ai')) {
            return null;
        }

        $lastError = null;

        foreach ($models as $model) {
            if ($this->isModelCoolingDown($model)) {
                continue;
            }

            $aiRequest = $this->aiRequests->start([
                'user_id' => $user?->id,
                'feature' => $feature,
                'provider' => $this->aiRequests->providerFromBaseUrl($baseUrl),
                'model' => $model,
                'metadata' => [
                    'engine' => $engine,
                    'plugins' => array_column($plugins, 'id'),
                ],
            ]);

            try {
                $result = $this->sendChatCompletion($apiKey, $baseUrl, $model, $messages, $plugins, $providerIgnore);
                $text = $this->normalize($result->content);

                if ($text === '' || $this->looksLikeUnreadableReply($text)) {
                    throw new RuntimeException('Model tidak mengembalikan teks materi yang bisa dipakai.');
                }

                $this->aiRequests->success($aiRequest, [
                    'input_tokens' => $result->inputTokens,
                    'output_tokens' => $result->outputTokens,
                    'response_id' => $result->responseId,
                ]);

                return [
                    'text' => $text,
                    'warning' => 'File dibaca dengan fallback AI OpenRouter karena extractor lokal belum cukup membaca isi file.',
                    'used_ocr' => true,
                    'engine' => $engine,
                    'model' => $model,
                ];
            } catch (Throwable $throwable) {
                $lastError = $throwable;
                $this->aiRequests->failed($aiRequest, $throwable);
                $this->cooldownModel($model, $throwable);
            }
        }

        return [
            'text' => '',
            'warning' => $lastError
                ? 'OpenRouter belum bisa membaca file ini sekarang: '.$lastError->getMessage()
                : 'OpenRouter belum bisa membaca file ini sekarang.',
            'used_ocr' => false,
            'engine' => null,
            'model' => null,
        ];
    }

    private function sendChatCompletion(string $apiKey, string $baseUrl, string $model, array $messages, array $plugins, array $providerIgnore): AiReplyResult
    {
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.1,
            'max_tokens' => (int) config('services.openai.content_max_output_tokens', 1800),
        ];

        if ($plugins !== []) {
            $payload['plugins'] = $plugins;
        }

        if ($providerIgnore !== []) {
            $payload['provider'] = [
                'ignore' => array_values($providerIgnore),
            ];
        }

        $response = Http::baseUrl($baseUrl)
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('services.openai.vision_fallback_timeout', config('services.openai.timeout', 60)))
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

        return new AiReplyResult(
            content: $content,
            inputTokens: $response->json('usage.prompt_tokens'),
            outputTokens: $response->json('usage.completion_tokens'),
            responseId: $response->json('id'),
        );
    }

    private function pdfModels(): array
    {
        return array_values(array_unique(array_filter([
            (string) config('services.openai.model', 'openai/gpt-oss-120b:free'),
            ...$this->visionModels(),
        ])));
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

        return array_values(array_unique(array_filter(array_map(fn ($model) => trim((string) $model), $models))));
    }

    private function dataUrl(UploadedFile $file, string $mimeType): string
    {
        return 'data:'.$mimeType.';base64,'.base64_encode((string) file_get_contents($file->getRealPath()));
    }

    private function isImage(string $extension): bool
    {
        return in_array($extension, ['png', 'jpg', 'jpeg', 'webp', 'tif', 'tiff', 'bmp'], true);
    }

    private function looksLikeUnreadableReply(string $content): bool
    {
        $normalized = str($content)->lower()->toString();

        return str_contains($normalized, 'tidak bisa melihat')
            || str_contains($normalized, 'tidak dapat melihat')
            || str_contains($normalized, 'tidak bisa membaca')
            || str_contains($normalized, 'tidak dapat membaca')
            || str_contains($normalized, 'cannot see')
            || str_contains($normalized, 'can\'t see')
            || str_contains($normalized, 'unable to view')
            || str_contains($normalized, 'unable to read');
    }

    private function isModelCoolingDown(string $model): bool
    {
        return Cache::has($this->cooldownKey($model));
    }

    private function cooldownModel(string $model, Throwable $exception): void
    {
        $seconds = (int) config('services.openai.vision_cooldown_seconds', 180);

        if ($seconds <= 0) {
            return;
        }

        Cache::put($this->cooldownKey($model), str($exception->getMessage())->limit(240)->toString(), now()->addSeconds($seconds));
    }

    private function cooldownKey(string $model): string
    {
        return 'ai:material-reader-cooldown:'.sha1($model);
    }

    private function normalize(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }
}
