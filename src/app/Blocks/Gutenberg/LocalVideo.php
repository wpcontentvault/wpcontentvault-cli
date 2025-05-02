<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;

class LocalVideo extends GutenbergBlock
{
    public function __construct(?string $content)
    {
        parent::__construct($content);
    }

    public function render(WordpressConfiguration $configuration): array
    {
        return [
            'blockName' => 'core/video',
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
        return "\n<figure class=\"wp-block-video\"><video autoplay controls loop src=\"{$this->content}\"></video></figure>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::LOCAL_VIDEO->value;
    }
}
