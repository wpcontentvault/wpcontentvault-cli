<?php

declare(strict_types=1);

namespace App\Context\AI;

class SelectCategoryResult
{
    public function __construct(
        public readonly string $content,
        public readonly string $comments,
        public readonly int    $inputTokens,
        public readonly int    $outputTokens,
    ) {}
}
