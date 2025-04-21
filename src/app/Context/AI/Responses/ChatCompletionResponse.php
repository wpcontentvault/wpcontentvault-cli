<?php

declare(strict_types=1);

namespace App\Context\AI\Responses;

use App\Enum\AI\FinishReason;

class ChatCompletionResponse
{
    public function __construct(
        public readonly FinishReason $finishReason,
        public readonly int $promptTokens,
        public readonly int $completionTokens,
        public readonly int $totalTokens,
        public readonly ?float $totalTime = null,
        public readonly ?string $content = null,
        public readonly array $toolCalls = [],
    ) {}
}
