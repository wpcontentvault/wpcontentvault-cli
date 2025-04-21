<?php

declare(strict_types=1);

namespace App\Context\AI\Responses;

class ToolCall
{
    public function __construct(
        public readonly string $id,
        public readonly string $function,
        public readonly array $arguments,
    ) {}
}
