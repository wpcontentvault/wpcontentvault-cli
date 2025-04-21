<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Inline;

use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class LinkRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): string
    {
        assert($block->getType() === BlockTypeEnum::LINK->value);

        $href = $block->getAttributes()['href'];

        if (str_starts_with($href, 'https://losst.pro')) {
            return '<a href="'.$href.'" target="_blank">'.$childRenderer->renderNodes($block->getChildren()).'</a>';
        }

        return '<a href="'.$href.'" target="_blank" rel="noreferrer noopener">'.$childRenderer->renderNodes($block->getChildren()).'</a>';

    }
}
