<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;

class YoutubeVideo extends GutenbergBlock
{
    public function __construct(?string $content)
    {
        parent::__construct($content);
    }

    public function render(): array
    {
        return [
            'blockName' => 'core/embed',
            'attrs' => [
                'url' => $this->content,
                'type' => 'video',
                'providerNameSlug' => 'youtube',
                'responsive' => true,
                'className' => 'wp-embed-aspect-16-9 wp-has-aspect-ratio',
            ],
            'innerBlocks' => [],
            'innerHTML' => $this->getHTML(),
            'innerContent' => [
                $this->getHTML(),
            ],
        ];
    }

    public function getHTML(): string
    {
        return "\n<figure class=\"wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio\"><div class=\"wp-block-embed__wrapper\">\n{$this->content}\n</div></figure>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::YOUTUBE_VIDEO->value;
    }
}
