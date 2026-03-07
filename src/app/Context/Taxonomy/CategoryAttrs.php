<?php

declare(strict_types=1);

namespace App\Context\Taxonomy;

class CategoryAttrs
{
    public function __construct(
        public readonly string  $slug,
    ) {}
}
