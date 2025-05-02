<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;

class Separator extends GutenbergBlock
{
    public function __construct()
    {
        parent::__construct(null);
    }

    public function render(WordpressConfiguration $configuration): array
    {
        return [
            'blockName' => null,
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => "\n\n",
            'innerContent' => [
                "\n\n",
            ],
        ];
    }

    public function getHTML(WordpressConfiguration $configuration): string
    {
        return '</br>';
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::SEPARATOR->value;
    }
}
