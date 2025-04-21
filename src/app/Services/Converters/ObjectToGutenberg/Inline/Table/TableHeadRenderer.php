<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Inline\Table;

use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class TableHeadRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): string
    {
        assert($block->getType() === BlockTypeEnum::TABLE_SECTION_HEAD->value);

        return '<thead>'.$childRenderer->renderNodes($block->getChildren()).'</thead>';
    }
}
