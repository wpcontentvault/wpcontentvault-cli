<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Locale;

class ArticleTagsUpdated extends ArticleEvent
{
    public function __construct(
        int                    $externalId,
        string                 $path,
        public readonly string $name
    )
    {
        parent::__construct($externalId, $path);
    }
}
