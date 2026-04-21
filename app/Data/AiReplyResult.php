<?php

namespace App\Data;

readonly class AiReplyResult
{
    public function __construct(
        public string $content,
        public ?int $inputTokens = null,
        public ?int $outputTokens = null,
        public ?string $responseId = null,
    ) {}
}
