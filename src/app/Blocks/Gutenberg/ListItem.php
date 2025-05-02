<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;

class ListItem extends GutenbergBlock
{
    public function render(WordpressConfiguration $configuration): array
    {
        return [
            'blockName' => 'core/list-item',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $this->getHTML($configuration),
            'innerContent' => [
                $this->getHTML($configuration),
            ],
        ];
    }

    public function getHTML(WordpressConfiguration $configuration): string
    {
        return "\n<li>{$this->content}</li>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::LIST_ITEM->value;
    }
}
