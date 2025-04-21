<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg;

use Illuminate\Support\Collection;

class ObjectToGutenbergConverter
{
    public function __construct(
        private HtmlRenderer $htmlRenderer,
    ) {}

    public function convert(Collection $blocks): Collection
    {
        return $this->htmlRenderer->renderRootNodes($blocks);
    }
}
