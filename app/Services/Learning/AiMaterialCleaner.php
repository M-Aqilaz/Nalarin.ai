<?php

namespace App\Services\Learning;

use App\Models\Material;
use App\Models\User;
use App\Services\Analytics\AiRequestLogger;
use Illuminate\Support\Facades\Http;

class AiMaterialCleaner
{
    public function __construct(private AiRequestLogger $aiRequests) {}

    public function clean(string $title, string $text, ?User $user = null): ?array
    {
        return $this->ask([
            [
                'role' => 'system',
                'content' => 'Kamu merapikan hasil OCR materi belajar. Jangan menambah fakta baru. Pertahankan istilah penting, rumus, daftar, dan struktur. Hapus noise OCR, perbaiki spasi/baris, dan buat heading yang jelas. Jawab hanya teks materi yang sudah rapi.',
            ],
            [
                'role' => 'user',
                'content' => "Judul materi: {$title}\n\nTeks OCR:\n".$this->trimForPrompt($text),
            ],
        ], 1200, 'material_cleaning', $user);
    }

    public function summarize(string $title, string $text, ?User $user = null, ?Material $material = null): ?array
    {
        return $this->ask([
            [
                'role' => 'system',
                'content' => 'Kamu membuat ringkasan belajar dalam bahasa Indonesia. Buat ringkasan terstruktur, mudah dipahami, dan cocok untuk siswa/mahasiswa. Jangan membuat informasi yang tidak ada di materi.',
            ],
            [
                'role' => 'user',
                'content' => "Judul materi: {$title}\n\nMateri:\n".$this->trimForPrompt($text),
            ],
        ], 900, 'summary', $user, $material);
    }

    private function ask(array $messages, int $maxTokens, string $feature, ?User $user = null, ?Material $material = null): ?array
    {
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return null;
        }

        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://openrouter.ai/api/v1'), '/');
        $model = (string) config('services.openai.model', 'openai/gpt-oss-120b:free');
        $aiRequest = $this->aiRequests->start([
            'user_id' => $user?->id,
            'material_id' => $material?->id,
            'feature' => $feature,
            'provider' => $this->aiRequests->providerFromBaseUrl($baseUrl),
            'model' => $model,
            'metadata' => [
                'max_tokens' => $maxTokens,
            ],
        ]);

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('services.openai.timeout', 60))
                ->acceptJson()
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name', 'Nalarin.ai'),
                ])
                ->post($baseUrl.'/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'max_tokens' => min($maxTokens, (int) config('services.openai.max_output_tokens', 800)),
                ]);

            if (! $response->successful()) {
                $this->aiRequests->failed($aiRequest, 'HTTP '.$response->status().': '.((string) $response->body()));

                return null;
            }

            $content = trim((string) data_get($response->json(), 'choices.0.message.content'));

            if ($content === '') {
                $this->aiRequests->failed($aiRequest, 'Provider AI tidak mengembalikan teks jawaban.');

                return null;
            }

            $this->aiRequests->success($aiRequest, [
                'input_tokens' => $response->json('usage.prompt_tokens'),
                'output_tokens' => $response->json('usage.completion_tokens'),
                'response_id' => $response->json('id'),
            ]);

            return [
                'text' => $content,
                'model' => $model,
            ];
        } catch (\Throwable $throwable) {
            $this->aiRequests->failed($aiRequest, $throwable);

            return null;
        }
    }

    private function trimForPrompt(string $text): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? $text;

        return mb_substr($normalized, 0, 16000);
    }
}
