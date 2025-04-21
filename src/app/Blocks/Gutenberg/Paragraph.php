<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;

class Paragraph extends GutenbergBlock
{
    public function render(): array
    {
        return [
            'blockName' => 'core/paragraph',
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
        $content = str_replace("\n", '<br>', $this->content);

        return "\n<p>{$content}</p>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::PARAGRAPH->value;
    }
}
