<?php

namespace App\Context\AI;

class SelectTagsResult
{
    public function __construct(
        public readonly array  $tags,
        public readonly string $comments,
        public readonly int    $inputTokens,
        public readonly int    $outputTokens,
    ) {}
}
