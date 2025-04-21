<?php

declare(strict_types=1);

namespace App\Services\Linting\Context;

class LintingResult
{
    public function __construct(
        public readonly string $status,
        public readonly array $errors,
    ) {}

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'errors' => $this->errors,
        ];
    }
}
