<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Block;

use App\Blocks\Gutenberg\Separator;
use App\Blocks\GutenbergBlock;
use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class NewlineRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): GutenbergBlock
    {
        assert($block->getType() === BlockTypeEnum::NEWLINE->value);

        return new Separator;
    }
}
