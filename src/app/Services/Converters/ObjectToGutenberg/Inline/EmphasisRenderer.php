<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Inline;

use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class EmphasisRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): string
    {
        assert($block->getType() === BlockTypeEnum::EMPHASIS->value);

        return '<em>'.$childRenderer->renderNodes($block->getChildren()).'</em>';
    }
}
