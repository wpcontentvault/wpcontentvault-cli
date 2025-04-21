<?php

declare(strict_types=1);

namespace App\Exceptions;

class AiDeserializationException extends AiException
{
    public function __construct(?int $lastErrorCode, ?string $lastErrorMessage)
    {
        parent::__construct("Failed to deserialize AI response! Error code: $lastErrorCode, Error message: $lastErrorMessage");
    }
}
