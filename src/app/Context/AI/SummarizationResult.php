<?php

declare(strict_types=1);

namespace App\Context\AI;

class SummarizationResult
{
    public function __construct(
        public readonly string $summary,
        public readonly int    $inputTokens,
        public readonly int    $outputTokens,
    ) {}
}
