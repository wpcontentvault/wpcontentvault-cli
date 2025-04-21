<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Block;

use App\Blocks\Gutenberg\Paragraph;
use App\Blocks\GutenbergBlock;
use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class ParagraphRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): GutenbergBlock
    {
        assert($block->getType() === BlockTypeEnum::PARAGRAPH->value);

        $image = $this->tryFetchImage($block);
        if ($image !== null && count($block->getChildren()) === 1) {
            return $childRenderer->renderNode($image);
        }
        $video = $this->tryFetchVideo($block);
        if ($video !== null && count($block->getChildren()) === 1) {
            return $childRenderer->renderNode($video);
        }

        $content = $childRenderer->renderNodes($block->getChildren());

        return new Paragraph($content);
    }

    private function tryFetchImage(ObjectBlock $block): ?ObjectBlock
    {
        foreach ($block->getChildren() as $item) {
            if ($item->getType() === BlockTypeEnum::IMAGE->value) {
                return $item;
            }
        }

        return null;
    }

    private function tryFetchVideo(ObjectBlock $block): ?ObjectBlock
    {
        foreach ($block->getChildren() as $item) {
            if ($item->getType() === BlockTypeEnum::VIDEO_LINK->value) {
                return $item;
            }
        }

        return null;
    }
}
