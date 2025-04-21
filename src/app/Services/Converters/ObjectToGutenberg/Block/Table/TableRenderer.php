<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Block\Table;

use App\Blocks\Gutenberg\Table;
use App\Blocks\GutenbergBlock;
use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class TableRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): GutenbergBlock
    {
        assert($block->getType() === BlockTypeEnum::TABLE->value);

        $content = $childRenderer->renderNodes($block->getChildren());

        return new Table($content);
    }
}
