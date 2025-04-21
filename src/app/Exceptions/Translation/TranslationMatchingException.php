<?php

declare(strict_types=1);

namespace App\Exceptions\Translation;

use RuntimeException;

class TranslationMatchingException extends RuntimeException
{
    public function __construct(
        string $message,
        ?string $originalBlockType,
        ?string $translationBlockType,
        ?string $originalContent,
        ?string $translationContent,
    ) {
        $message .= " | Original block type: $originalBlockType, Translation block type: $translationBlockType | Original content: $originalContent, Translation content: $translationContent";

        parent::__construct($message);
    }
}
