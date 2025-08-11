<?php

declare(strict_types=1);

namespace App\Context\Classification;

class ArticleContent
{
    public function __construct(
        public readonly string $title,
        public readonly string $annotation,
        public readonly string $warpingUp,
    ) {}
}
