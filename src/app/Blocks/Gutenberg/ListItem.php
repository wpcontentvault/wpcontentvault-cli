<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;

class ListItem extends GutenbergBlock
{
    public function render(): array
    {
        return [
            'blockName' => 'core/list-item',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $this->getHTML(),
            'innerContent' => [
                $this->getHTML(),
            ],
        ];
    }

    public function getHTML(): string
    {
        return "\n<li>{$this->content}</li>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::LIST_ITEM->value;
    }
}
