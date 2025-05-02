<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;
use Doctrine\Inflector\Rules\Word;

class Table extends GutenbergBlock
{
    public function __construct(?string $content)
    {
        $content = str_replace("\n", '', $content);
        parent::__construct($content);
    }

    public function render(WordpressConfiguration $configuration): array
    {
        return [
            'blockName' => 'core/table',
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
        return "\n<figure class=\"wp-block-table\"><table class=\"has-fixed-layout\">{$this->content}</table></figure>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::SEPARATOR->value;
    }
}
