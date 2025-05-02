<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;
use Doctrine\Inflector\Rules\Word;

class Heading extends GutenbergBlock
{
    private int $level;

    public function __construct(int $level, ?string $content)
    {
        parent::__construct($content);

        $this->level = $level;
    }

    public function render(WordpressConfiguration $configuration): array
    {
        return [
            'blockName' => 'core/heading',
            'attrs' => [
                'level' => $this->level,
            ],
            'innerBlocks' => [],
            'innerHTML' => $this->getHTML($configuration),
            'innerContent' => [
                $this->getHTML($configuration),
            ],
        ];
    }

    public function getHTML(WordpressConfiguration $configuration): string
    {
        return "\n<h{$this->level} class=\"wp-block-heading\">{$this->content}</h{$this->level}>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::HEADING->value;
    }
}
