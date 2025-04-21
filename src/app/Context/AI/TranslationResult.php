<?php

declare(strict_types=1);

namespace App\Context\AI;

class TranslationResult
{
    public function __construct(
        public readonly string $text,
        public readonly string $comments,
        public readonly int $inputTokens,
        public readonly int $outputTokens,
    ) {}
}
