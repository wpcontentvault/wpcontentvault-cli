<?php

declare(strict_types=1);

namespace App\Context\Translation;

class TranslationHistoryItem
{
    public function __construct(
        public string $original,
        public string $translation,
    ) {}
}
