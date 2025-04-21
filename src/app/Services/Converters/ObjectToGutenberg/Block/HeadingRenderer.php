<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Block;

use App\Blocks\Gutenberg\Heading;
use App\Blocks\GutenbergBlock;
use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;
use RuntimeException;

class HeadingRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): GutenbergBlock
    {
        assert($block->getType() === BlockTypeEnum::HEADING->value);

        if ($this->hasChildImage($block)) {
            throw new RuntimeException('Heading should not contain embedded image!');
        }

        $content = $childRenderer->renderNodes($block->getChildren());
        $level = $block->getAttributes()['level'];

        return new Heading($level, $content);
    }

    private function hasChildImage(ObjectBlock $block): bool
    {
        foreach ($block->getChildren() as $item) {
            if ($item->getType() === BlockTypeEnum::IMAGE->value) {
                return true;
            }
        }

        return false;
    }
}
