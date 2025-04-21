<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;

class Table extends GutenbergBlock
{
    public function __construct(?string $content)
    {
        $content = str_replace("\n", '', $content);
        parent::__construct($content);
    }

    public function render(): array
    {
        return [
            'blockName' => 'core/table',
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
        return "\n<figure class=\"wp-block-table\"><table class=\"has-fixed-layout\">{$this->content}</table></figure>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::SEPARATOR->value;
    }
}
