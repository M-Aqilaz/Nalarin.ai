<?php

namespace App\Services\Learning;

use Illuminate\Support\Facades\Http;

class AiMaterialCleaner
{
    public function clean(string $title, string $text): ?array
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
        ], 1200);
    }

    public function summarize(string $title, string $text): ?array
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
        ], 900);
    }

    private function ask(array $messages, int $maxTokens): ?array
    {
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return null;
        }

        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://openrouter.ai/api/v1'), '/');
        $model = (string) config('services.openai.model', 'openai/gpt-oss-120b:free');

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('services.openai.timeout', 60))
                ->acceptJson()
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name', 'Pelajarin.ai'),
                ])
                ->post($baseUrl.'/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'max_tokens' => min($maxTokens, (int) config('services.openai.max_output_tokens', 800)),
                ]);

            if (! $response->successful()) {
                return null;
            }

            $content = trim((string) data_get($response->json(), 'choices.0.message.content'));

            if ($content === '') {
                return null;
            }

            return [
                'text' => $content,
                'model' => $model,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function trimForPrompt(string $text): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? $text;

        return mb_substr($normalized, 0, 16000);
    }
}
