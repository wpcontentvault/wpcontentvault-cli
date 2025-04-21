<?php

declare(strict_types=1);

namespace App\Context\Translation;

class TranslationOptions
{
    public function __construct(
        public readonly bool $isHeading = false,
        public readonly bool $isLastHeading = false,
        public readonly ?string $context = null,
    ) {}
}
