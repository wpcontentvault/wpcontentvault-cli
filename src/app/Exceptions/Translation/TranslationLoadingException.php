<?php

declare(strict_types=1);

namespace App\Exceptions\Translation;

use RuntimeException;

class TranslationLoadingException extends RuntimeException
{
    public function __construct(
        string $message,
    ) {
        parent::__construct($message);
    }
}
