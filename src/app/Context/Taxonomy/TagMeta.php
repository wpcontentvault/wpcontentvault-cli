<?php

declare(strict_types=1);

namespace App\Context\Taxonomy;

class TagMeta
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $url = null,
        public readonly ?int    $externalId = null,
        public readonly ?string $slug = null,
    ) {}
}
