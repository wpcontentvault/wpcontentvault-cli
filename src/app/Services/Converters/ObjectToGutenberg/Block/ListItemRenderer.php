<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Block;

use App\Blocks\Gutenberg\ListItem;
use App\Blocks\GutenbergBlock;
use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class ListItemRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): GutenbergBlock
    {
        assert($block->getType() === BlockTypeEnum::LIST_ITEM->value);

        $content = $childRenderer->renderNodes($block->getChildren());

        return new ListItem($content);
    }
}
