<?php

namespace App\Services\Learning;

use App\Models\Material;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudyContentGenerator
{
    public function generateFlashcards(Material $material, int $limit = 12, array $avoidFronts = []): array
    {
        $seed = $this->generationSeed();
        $aiCards = $this->generateAiFlashcards($material, $limit, $avoidFronts, $seed);

        if (count($aiCards) >= 4) {
            return $aiCards;
        }

        $pairs = $this->buildKnowledgePairs($material->raw_text ?? '', $limit + count($avoidFronts) + 4, $seed);
        $avoid = collect($avoidFronts)->map(fn (string $value): string => Str::lower(trim($value)))->filter()->all();

        $pairs = array_values(array_filter($pairs, fn (array $pair): bool => ! in_array(Str::lower($pair['front']), $avoid, true)));

        return array_values(array_map(function (array $pair, int $index): array {
            return [
                'front' => $pair['front'],
                'back' => $pair['back'],
                'example' => $pair['example'],
                'difficulty' => $pair['difficulty'],
                'sort_order' => $index + 1,
            ];
        }, array_slice($pairs, 0, $limit), array_keys(array_slice($pairs, 0, $limit))));
    }

    public function generateQuiz(Material $material, int $limit = 10, array $avoidPrompts = []): array
    {
        $seed = $this->generationSeed();
        $aiQuestions = $this->generateAiQuiz($material, $limit, $avoidPrompts, $seed);

        if (count($aiQuestions) >= 4) {
            return $aiQuestions;
        }

        $pairs = collect($this->buildKnowledgePairs($material->raw_text ?? '', max($limit + count($avoidPrompts) + 6, 10), $seed));

        if ($pairs->count() < 4) {
            return [];
        }

        $avoid = collect($avoidPrompts)->map(fn (string $value): string => $this->fingerprint($value))->filter()->all();

        return $pairs
            ->values()
            ->map(function (array $pair, int $index) use ($pairs): array {
                $type = $index % 2 === 0 ? 'definition' : 'term';

                return $type === 'definition'
                    ? $this->buildDefinitionQuestion($pair, $pairs, $index)
                    : $this->buildTermQuestion($pair, $pairs, $index);
            })
            ->reject(fn (array $question): bool => in_array($this->fingerprint($question['prompt']), $avoid, true))
            ->take($limit)
            ->values()
            ->all();
    }

    private function generateAiFlashcards(Material $material, int $limit, array $avoidFronts, string $seed): array
    {
        $payload = $this->askAi([
            [
                'role' => 'system',
                'content' => 'Kamu membuat flashcard belajar dari materi. Output wajib JSON valid saja. Jangan markdown. Flashcard harus singkat, jelas, dan berbasis materi. Front wajib berupa istilah/konsep spesifik dari materi, bukan kalimat tanya, bukan "Konsep 1", bukan judul umum, dan bukan istilah yang tidak ada di materi. Back wajib definisi singkat 1 kalimat maksimal 24 kata. Example opsional 1 kalimat pendek dari konteks materi.',
            ],
            [
                'role' => 'user',
                'content' => 'Buat '.$limit.' flashcard dari materi berikut. Variasi seed: '.$seed.'. Hindari istilah yang sudah pernah dibuat: '.$this->avoidList($avoidFronts).'. Format JSON: {"flashcards":[{"front":"Istilah jelas","back":"Definisi singkat","example":"Konteks pendek","difficulty":"Dasar|Menengah|Sulit"}]}.'."\n\nMateri:\n".$this->trimForPrompt($material->raw_text ?? ''),
            ],
        ]);

        $items = data_get($payload, 'flashcards', []);

        if (! is_array($items)) {
            return [];
        }

        return collect($items)
            ->map(fn ($item, int $index): ?array => $this->normalizeAiFlashcard($item, $index, $material->raw_text ?? '', $avoidFronts))
            ->filter()
            ->unique(fn (array $card): string => Str::lower($card['front']))
            ->take($limit)
            ->values()
            ->all();
    }

    private function generateAiQuiz(Material $material, int $limit, array $avoidPrompts, string $seed): array
    {
        $payload = $this->askAi([
            [
                'role' => 'system',
                'content' => 'Kamu membuat kuis pilihan ganda dari materi. Output wajib JSON valid saja. Jangan markdown. Tiap soal harus baru, spesifik ke materi, punya 4 opsi unik, hanya 1 jawaban benar, correct_choice harus index 0-3 yang menunjuk opsi benar, dan explanation harus menjelaskan kenapa jawaban itu benar berdasarkan materi.',
            ],
            [
                'role' => 'user',
                'content' => 'Buat '.$limit.' soal kuis dari materi berikut. Variasi seed: '.$seed.'. Jangan ulangi pertanyaan ini: '.$this->avoidList($avoidPrompts).'. Format JSON: {"questions":[{"prompt":"Pertanyaan","choices":["A","B","C","D"],"correct_choice":0,"explanation":"Alasan singkat"}]}.'."\n\nMateri:\n".$this->trimForPrompt($material->raw_text ?? ''),
            ],
        ]);

        $items = data_get($payload, 'questions', []);

        if (! is_array($items)) {
            return [];
        }

        return collect($items)
            ->map(fn ($item, int $index): ?array => $this->normalizeAiQuestion($item, $index, $material->raw_text ?? '', $avoidPrompts))
            ->filter()
            ->take($limit)
            ->values()
            ->all();
    }

    private function askAi(array $messages): ?array
    {
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('services.openai.timeout', 60))
                ->acceptJson()
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name', 'Pelajarin.ai'),
                ])
                ->post(rtrim((string) config('services.openai.base_url', 'https://openrouter.ai/api/v1'), '/').'/chat/completions', [
                    'model' => (string) config('services.openai.model', 'openai/gpt-oss-120b:free'),
                    'messages' => $messages,
                    'temperature' => 0.55,
                    'top_p' => 0.85,
                    'max_tokens' => (int) config('services.openai.content_max_output_tokens', 1800),
                ]);

            if (! $response->successful()) {
                return null;
            }

            $content = trim((string) data_get($response->json(), 'choices.0.message.content'));
            $decoded = json_decode($this->extractJson($content), true);

            return is_array($decoded) ? $decoded : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeAiFlashcard(mixed $item, int $index, string $sourceText, array $avoidFronts): ?array
    {
        if (! is_array($item)) {
            return null;
        }

        $front = trim((string) ($item['front'] ?? ''));
        $back = $this->limitWords(trim((string) ($item['back'] ?? '')), 24);
        $example = $this->limitWords(trim((string) ($item['example'] ?? '')), 22);
        $difficulty = trim((string) ($item['difficulty'] ?? 'Menengah'));

        if (! $this->isValidFront($front) || $this->wordCount($back) < 4 || ! $this->isGroundedTerm($front, $sourceText) || $this->isAvoided($front, $avoidFronts)) {
            return null;
        }

        if (! in_array($difficulty, ['Dasar', 'Menengah', 'Sulit'], true)) {
            $difficulty = $this->resolveDifficulty($back);
        }

        return [
            'front' => Str::title($front),
            'back' => $back,
            'example' => $example !== '' ? $example : null,
            'difficulty' => $difficulty,
            'sort_order' => $index + 1,
        ];
    }

    private function normalizeAiQuestion(mixed $item, int $index, string $sourceText, array $avoidPrompts): ?array
    {
        if (! is_array($item)) {
            return null;
        }

        $prompt = trim((string) ($item['prompt'] ?? ''));
        $choices = collect($item['choices'] ?? [])
            ->map(fn ($choice): string => trim((string) $choice))
            ->filter()
            ->unique(fn (string $choice): string => Str::lower($choice))
            ->values();
        $correctChoice = (int) ($item['correct_choice'] ?? -1);
        $explanation = $this->limitWords(trim((string) ($item['explanation'] ?? '')), 36);

        if ($prompt === '' || $choices->count() !== 4 || $correctChoice < 0 || $correctChoice > 3 || ! isset($choices[$correctChoice]) || $this->isAvoided($prompt, $avoidPrompts)) {
            return null;
        }

        if (! $this->hasGroundedChoice($choices->all(), $sourceText)) {
            return null;
        }

        if ($explanation === '') {
            $explanation = 'Jawaban benar sesuai konteks materi yang diberikan.';
        }

        return [
            'prompt' => $prompt,
            'choices' => $choices->all(),
            'correct_choice' => $correctChoice,
            'explanation' => $explanation,
            'sort_order' => $index + 1,
        ];
    }

    private function extractJson(string $content): string
    {
        if (str_starts_with($content, '{') && str_ends_with($content, '}')) {
            return $content;
        }

        if (preg_match('/\{.*\}/s', $content, $matches)) {
            return $matches[0];
        }

        return $content;
    }

    private function trimForPrompt(string $text): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? $text;

        return mb_substr($normalized, 0, 18000);
    }

    private function buildKnowledgePairs(string $text, int $limit, ?string $seed = null): array
    {
        $normalizedText = $this->normalizeText($text);

        if ($normalizedText === '') {
            return [];
        }

        $lines = collect(preg_split("/\n+/", $normalizedText) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values();

        $sentences = collect(preg_split('/(?<=[.!?])\s+/', $normalizedText) ?: [])
            ->map(fn (string $sentence) => trim($sentence))
            ->filter(fn (string $sentence) => Str::length($sentence) >= 40)
            ->values();

        $cards = collect()
            ->merge($this->extractLinePairs($lines))
            ->merge($this->extractDefinitionPairs($sentences));

        if ($cards->count() < $limit) {
            $cards = $cards->merge($this->extractKeywordPairs($sentences, $cards->pluck('front')->all(), $limit - $cards->count()));
        }

        if ($cards->count() < 4) {
            $cards = $cards->merge($this->extractFallbackPairs($sentences, 4 - $cards->count()));
        }

        return $cards
            ->unique(fn (array $card) => Str::lower($card['front']))
            ->when($seed, fn (Collection $items): Collection => $this->rotateCollection($items, $seed))
            ->take($limit)
            ->values()
            ->all();
    }

    private function extractLinePairs(Collection $lines): Collection
    {
        return $lines
            ->map(function (string $line): ?array {
                if (! str_contains($line, ':')) {
                    return null;
                }

                [$front, $back] = array_pad(explode(':', $line, 2), 2, '');
                $front = trim($front);
                $back = $this->limitWords(trim($back), 28);

                if (! $this->isValidFront($front) || $this->wordCount($back) < 5) {
                    return null;
                }

                return $this->makePair($front, $back, $back);
            })
            ->filter()
            ->values();
    }

    private function extractDefinitionPairs(Collection $sentences): Collection
    {
        $patterns = [
            '/^(?<front>[\pL\pN][\pL\pN\s\-\/]{1,80}?)\s+(?:adalah|ialah|merupakan|yaitu|yakni|is|are|refers to)\s+(?<back>.+)$/ui',
            '/^(?<front>[\pL\pN][\pL\pN\s\-\/]{1,80}?)\s*-\s*(?<back>.+)$/u',
        ];

        return $sentences
            ->map(function (string $sentence) use ($patterns): ?array {
                foreach ($patterns as $pattern) {
                    if (! preg_match($pattern, $sentence, $matches)) {
                        continue;
                    }

                    $front = trim($matches['front']);
                    $back = $this->limitWords(trim($matches['back']), 28);

                    if (! $this->isValidFront($front) || $this->wordCount($back) < 5) {
                        continue;
                    }

                    return $this->makePair($front, $back, $sentence);
                }

                return null;
            })
            ->filter()
            ->values();
    }

    private function extractKeywordPairs(Collection $sentences, array $usedFronts, int $limit): Collection
    {
        $used = collect($usedFronts)->map(fn (string $value) => Str::lower($value))->all();
        $keywords = $this->extractKeywords($sentences->implode(' '), $limit * 3);

        return collect($keywords)
            ->map(function (string $keyword) use ($sentences, $used): ?array {
                if (in_array(Str::lower($keyword), $used, true)) {
                    return null;
                }

                $sentence = $sentences->first(fn (string $item) => Str::contains(Str::lower($item), Str::lower($keyword)));

                if (! $sentence) {
                    return null;
                }

                return $this->makePair(Str::headline($keyword), $this->limitWords($sentence, 28), $sentence);
            })
            ->filter()
            ->take($limit)
            ->values();
    }

    private function extractFallbackPairs(Collection $sentences, int $limit): Collection
    {
        return $sentences
            ->take($limit)
            ->values()
            ->map(fn (string $sentence, int $index): array => $this->makePair('Konsep ' . ($index + 1), $this->limitWords($sentence, 28), $sentence));
    }

    private function buildDefinitionQuestion(array $pair, Collection $pairs, int $index): array
    {
        $correct = $pair['back'];
        $distractors = $pairs->where('front', '!=', $pair['front'])->pluck('back')->filter(fn (string $value) => $value !== $correct)->unique()->take(3)->values();
        $options = $distractors->push($correct)->shuffle()->values();

        return [
            'prompt' => 'Apa definisi yang paling tepat untuk "' . $pair['front'] . '"?',
            'choices' => $options->all(),
            'correct_choice' => (int) $options->search($correct),
            'explanation' => $pair['example'] !== '' ? $pair['example'] : $pair['back'],
            'sort_order' => $index + 1,
        ];
    }

    private function buildTermQuestion(array $pair, Collection $pairs, int $index): array
    {
        $correct = $pair['front'];
        $distractors = $pairs->where('front', '!=', $pair['front'])->pluck('front')->filter(fn (string $value) => $value !== $correct)->unique()->take(3)->values();
        $options = $distractors->push($correct)->shuffle()->values();

        return [
            'prompt' => 'Istilah apa yang paling sesuai dengan deskripsi berikut? "' . $pair['back'] . '"',
            'choices' => $options->all(),
            'correct_choice' => (int) $options->search($correct),
            'explanation' => $pair['example'] !== '' ? $pair['example'] : $pair['back'],
            'sort_order' => $index + 1,
        ];
    }

    private function extractKeywords(string $text, int $limit): array
    {
        $stopwords = ['yang', 'dan', 'atau', 'dengan', 'untuk', 'dari', 'pada', 'dalam', 'karena', 'adalah', 'ialah', 'merupakan', 'tersebut', 'sebagai', 'juga', 'akan', 'agar', 'lebih', 'kita', 'mereka', 'kami', 'anda', 'this', 'that', 'with', 'from', 'into', 'about', 'after', 'before', 'during', 'while', 'there', 'their', 'have', 'has', 'been', 'were', 'what', 'when', 'where', 'which', 'melalui', 'antara', 'oleh', 'pembelajaran', 'materi', 'dapat', 'dalamnya', 'suatu', 'secara', 'proses', 'contoh', 'informasi'];
        $tokens = preg_split('/[^[:alnum:]\pL]+/u', Str::lower($text)) ?: [];
        $frequencies = [];

        foreach ($tokens as $token) {
            if (Str::length($token) < 5 || in_array($token, $stopwords, true)) {
                continue;
            }

            $frequencies[$token] = ($frequencies[$token] ?? 0) + 1;
        }

        arsort($frequencies);

        return array_slice(array_keys($frequencies), 0, $limit);
    }

    private function makePair(string $front, string $back, string $example): array
    {
        return [
            'front' => Str::title(trim($front)),
            'back' => trim($back),
            'example' => trim($example),
            'difficulty' => $this->resolveDifficulty($back),
        ];
    }

    private function isValidFront(string $front): bool
    {
        $wordCount = $this->wordCount($front);

        return $wordCount >= 1
            && $wordCount <= 8
            && Str::length($front) <= 80
            && ! preg_match('/\d{4,}/', $front)
            && ! str_contains($front, '?')
            && ! preg_match('/^(konsep|materi|topik|hal penting|pengertian)$/iu', trim($front));
    }

    private function resolveDifficulty(string $text): string
    {
        $wordCount = $this->wordCount($text);

        return match (true) {
            $wordCount >= 22 => 'Sulit',
            $wordCount >= 13 => 'Menengah',
            default => 'Dasar',
        };
    }

    private function limitWords(string $text, int $words): string
    {
        return Str::of(trim($text))->words($words, '...')->value();
    }

    private function normalizeText(string $text): string
    {
        $text = preg_replace("/\r\n|\r/", "\n", $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function wordCount(string $text): int
    {
        $tokens = preg_split('/[^\pL\pN]+/u', trim($text)) ?: [];

        return count(array_filter($tokens));
    }

    private function generationSeed(): string
    {
        return now()->format('YmdHis').'-'.Str::random(8);
    }

    private function avoidList(array $items): string
    {
        $values = collect($items)
            ->map(fn (string $value): string => trim($value))
            ->filter()
            ->take(20)
            ->values()
            ->all();

        return $values === [] ? '-' : implode('; ', $values);
    }

    private function isAvoided(string $value, array $avoid): bool
    {
        $fingerprint = $this->fingerprint($value);

        return collect($avoid)
            ->map(fn (string $item): string => $this->fingerprint($item))
            ->contains($fingerprint);
    }

    private function fingerprint(string $value): string
    {
        $normalized = Str::lower(preg_replace('/[^\pL\pN]+/u', ' ', trim($value)) ?? $value);

        return trim((string) Str::of($normalized)->squish());
    }

    private function isGroundedTerm(string $front, string $sourceText): bool
    {
        $source = Str::lower($sourceText);
        $front = Str::lower($front);

        if (Str::contains($source, $front)) {
            return true;
        }

        $words = collect(preg_split('/[^\pL\pN]+/u', $front) ?: [])
            ->filter(fn (string $word): bool => Str::length($word) >= 4)
            ->values();

        return $words->isNotEmpty() && $words->every(fn (string $word): bool => Str::contains($source, Str::lower($word)));
    }

    private function hasGroundedChoice(array $choices, string $sourceText): bool
    {
        $source = Str::lower($sourceText);

        return collect($choices)
            ->flatMap(fn (string $choice): array => preg_split('/[^\pL\pN]+/u', Str::lower($choice)) ?: [])
            ->filter(fn (string $word): bool => Str::length($word) >= 5)
            ->contains(fn (string $word): bool => Str::contains($source, $word));
    }

    private function rotateCollection(Collection $items, string $seed): Collection
    {
        if ($items->isEmpty()) {
            return $items;
        }

        $offset = crc32($seed) % $items->count();

        return $items->slice($offset)->merge($items->slice(0, $offset))->values();
    }
}
