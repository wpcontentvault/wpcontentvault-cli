<?php

declare(strict_types=1);

namespace App\Services\Sitemap\Context;

class AlternateLink
{
    public function __construct(
        public readonly string $url,
        public readonly string $locale,
    ) {}
}
