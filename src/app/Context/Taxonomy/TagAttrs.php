<?php

declare(strict_types=1);

namespace App\Context\Taxonomy;

use App\Models\TagCategory;

class TagAttrs
{
    public function __construct(
        public readonly string      $slug,
        public readonly TagCategory $category,
        public readonly ?string      $description = null,
    ) {}
}
