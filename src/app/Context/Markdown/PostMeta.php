<?php

declare(strict_types=1);

namespace App\Context\Markdown;

use App\Models\Category;
use App\Models\Locale;
use Carbon\CarbonImmutable;

class PostMeta
{
    public function __construct(
        public readonly Locale $locale,
        public readonly string $title,
        public readonly string $status,
        public readonly ?string $author,
        public readonly ?CarbonImmutable $publishedAt,
        public readonly ?CarbonImmutable $modifiedAt,
        public readonly ?string $url = null,
        public readonly ?int $externalId = null,
        public readonly ?Category $category = null,
        public readonly array $tags = [],
        public readonly ?string $serializedId = null,
    ) {}
}
