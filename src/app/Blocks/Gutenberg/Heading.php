<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;

class Heading extends GutenbergBlock
{
    private int $level;

    public function __construct(int $level, ?string $content)
    {
        parent::__construct($content);

        $this->level = $level;
    }

    public function render(): array
    {
        return [
            'blockName' => 'core/heading',
            'attrs' => [
                'level' => $this->level,
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
        return "\n<h{$this->level} class=\"wp-block-heading\">{$this->content}</h{$this->level}>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::HEADING->value;
    }
}
