<?php

namespace App\Services\Learning;

use App\Models\Material;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudyContentGenerator
{
    /**
     * @return array<int, array<string, string>>
     */
    public function generateFlashcards(Material $material, int $limit = 12): array
    {
        $pairs = $this->buildKnowledgePairs($material->raw_text ?? '', $limit);

        return array_values(array_map(function (array $pair, int $index): array {
            return [
                'front' => $pair['front'],
                'back' => $pair['back'],
                'example' => $pair['example'],
                'difficulty' => $pair['difficulty'],
                'sort_order' => $index + 1,
            ];
        }, $pairs, array_keys($pairs)));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function generateQuiz(Material $material, int $limit = 10): array
    {
        $pairs = collect($this->buildKnowledgePairs($material->raw_text ?? '', max($limit + 2, 8)));

        if ($pairs->count() < 4) {
            return [];
        }

        return $pairs
            ->take($limit)
            ->values()
            ->map(function (array $pair, int $index) use ($pairs): array {
                $type = $index % 2 === 0 ? 'definition' : 'term';

                return $type === 'definition'
                    ? $this->buildDefinitionQuestion($pair, $pairs, $index)
                    : $this->buildTermQuestion($pair, $pairs, $index);
            })
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buildKnowledgePairs(string $text, int $limit): array
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

        $cards = collect();

        $cards = $cards
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

                return $this->makePair(
                    Str::headline($keyword),
                    $this->limitWords($sentence, 28),
                    $sentence,
                );
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
            ->map(function (string $sentence, int $index): array {
                return $this->makePair(
                    'Konsep ' . ($index + 1),
                    $this->limitWords($sentence, 28),
                    $sentence,
                );
            });
    }

    private function buildDefinitionQuestion(array $pair, Collection $pairs, int $index): array
    {
        $correct = $pair['back'];
        $distractors = $pairs
            ->where('front', '!=', $pair['front'])
            ->pluck('back')
            ->filter(fn (string $value) => $value !== $correct)
            ->unique()
            ->take(3)
            ->values();

        $options = $distractors
            ->push($correct)
            ->shuffle()
            ->values();

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
        $distractors = $pairs
            ->where('front', '!=', $pair['front'])
            ->pluck('front')
            ->filter(fn (string $value) => $value !== $correct)
            ->unique()
            ->take(3)
            ->values();

        $options = $distractors
            ->push($correct)
            ->shuffle()
            ->values();

        return [
            'prompt' => 'Istilah apa yang paling sesuai dengan deskripsi berikut? "' . $pair['back'] . '"',
            'choices' => $options->all(),
            'correct_choice' => (int) $options->search($correct),
            'explanation' => $pair['example'] !== '' ? $pair['example'] : $pair['back'],
            'sort_order' => $index + 1,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function extractKeywords(string $text, int $limit): array
    {
        $stopwords = [
            'yang', 'dan', 'atau', 'dengan', 'untuk', 'dari', 'pada', 'dalam', 'karena',
            'adalah', 'ialah', 'merupakan', 'tersebut', 'sebagai', 'juga', 'akan', 'agar',
            'lebih', 'kita', 'mereka', 'kami', 'anda', 'this', 'that', 'with', 'from',
            'into', 'about', 'after', 'before', 'during', 'while', 'there', 'their',
            'have', 'has', 'been', 'were', 'what', 'when', 'where', 'which', 'melalui',
            'antara', 'oleh', 'pembelajaran', 'materi', 'dapat', 'dalamnya', 'suatu',
            'secara', 'proses', 'contoh', 'informasi',
        ];

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
            && ! preg_match('/\d{4,}/', $front);
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
        return Str::of(trim($text))
            ->words($words, '...')
            ->value();
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
}
