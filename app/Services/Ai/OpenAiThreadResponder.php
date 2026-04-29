<?php

namespace App\Services\Ai;

use App\Contracts\AiThreadResponder;
use App\Data\AiReplyResult;
use App\Models\ChatThread;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

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
                    'content' => $message->content,
                ])
        )->values()->all();

        $response = $this->client($apiKey, $baseUrl)
            ->withHeaders(array_filter([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name', 'Nalarin.ai'),
            ]))
            ->post('/chat/completions', [
                'model' => config('services.openai.model', 'openai/gpt-oss-120b:free'),
                'messages' => $messages,
                'max_tokens' => (int) config('services.openai.max_output_tokens', 800),
            ]);

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
            'Anda adalah tutor belajar untuk Nalarin.ai.',
            'Jawab dalam Bahasa Indonesia yang jelas, ringkas, dan fokus pada bantuan belajar.',
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
