<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;

class LocalVideo extends GutenbergBlock
{
    public function __construct(?string $content)
    {
        parent::__construct($content);
    }

    public function render(): array
    {
        return [
            'blockName' => 'core/video',
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
        return "\n<figure class=\"wp-block-video\"><video autoplay controls loop src=\"{$this->content}\"></video></figure>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::LOCAL_VIDEO->value;
    }
}
