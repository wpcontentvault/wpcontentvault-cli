<?php

declare(strict_types=1);

namespace App\Events;

abstract class ArticleEvent
{
    public function __construct(
        public readonly int $externalId,
        public readonly string $path,
    ) {}
}
