<?php

declare(strict_types=1);

namespace App\Context\Taxonomy;

class TagMeta
{
    public function __construct(
        public readonly string      $name,
        public readonly ?int        $externalId = null,
    ) {}
}
