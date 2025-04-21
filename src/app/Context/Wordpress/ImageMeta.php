<?php

declare(strict_types=1);

namespace App\Context\Wordpress;

class ImageMeta
{
    public function __construct(
        public readonly int $externalId,
        public readonly string $externalUrl,
        // Must be nullable in case media is video which does not have thumbnail
        public readonly ?string $thumbnailUrl
    ) {}
}
