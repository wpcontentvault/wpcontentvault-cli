<?php

declare(strict_types=1);

namespace App\Context\AI;

class ChatCompletionResult
{
    public function __construct(
        public readonly string $content,
        public readonly string $reasoning,
        public readonly int $inputTokens,
        public readonly int $outputTokens,
    ) {}
}
