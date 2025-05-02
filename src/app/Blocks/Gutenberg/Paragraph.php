<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;

class Paragraph extends GutenbergBlock
{
    public function render(WordpressConfiguration $configuration): array
    {
        return [
            'blockName' => 'core/paragraph',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $this->getHTML($configuration),
            'innerContent' => [
                $this->getHTML($configuration),
            ],
        ];
    }

    public function getHTML(WordpressConfiguration $configuration): string
    {
        $content = str_replace("\n", '<br>', $this->content);

        return "\n<p>{$content}</p>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::PARAGRAPH->value;
    }
}
