<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Block;

use App\Blocks\Gutenberg\ListBlock;
use App\Blocks\GutenbergBlock;
use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class ListBlockRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): GutenbergBlock
    {
        assert($block->getType() === BlockTypeEnum::LIST->value);

        $items = [];
        foreach ($block->getChildren() as $child) {
            if ($child->getType() === BlockTypeEnum::LIST_ITEM->value) {
                $items[] = $childRenderer->renderNode($child);
            }
        }

        return new ListBlock('', $items);
    }
}
