<?php

declare(strict_types=1);

namespace App\Context;

class LocaleOptions
{
    public function __construct(
        public readonly bool $shouldCapitalizeTitle,
        public readonly bool $shouldHaveTranslatedByAiLabel,
        public readonly ?string $customConsolationsLabel,
    ) {}
}
