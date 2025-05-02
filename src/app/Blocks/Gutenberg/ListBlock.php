<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;

class ListBlock extends GutenbergBlock
{
    private array $items = [];

    public function __construct(?string $content, array $items)
    {
        parent::__construct($content);

        $this->items = $items;
    }

    public function render(WordpressConfiguration $configuration): array
    {
        $innerBlocks = [];

        foreach ($this->items as $item) {
            \assert($item instanceof ListItem);

            $innerBlocks[] = $item->render($configuration);
        }

        return [
            'blockName' => 'core/list',
            'attrs' => [],
            'innerBlocks' => $innerBlocks,
            'innerHTML' => "\n<ul class=\"wp-block-list\">".str_repeat("\n\n", count($this->items) - 1)."</ul>\n",
            'innerContent' => $this->getInnerContent(),
        ];
    }

    public function getInnerContent(): array
    {
        $content = [];
        $content[] = "\n<ul class=\"wp-block-list\">";
        $isFirst = true;
        foreach ($this->items as $item) {
            if ($isFirst === false) {
                $content[] = "\n\n";
            }

            $content[] = null;

            $isFirst = false;
        }

        $content[] = "</ul>\n";

        return $content;
    }

    public function getHTML(WordpressConfiguration $configuration): string
    {
        $content = "<ul>\n";

        foreach ($this->items as $item) {
            /** @var ListItem $item */
            $content .= $item->getHTML($configuration)."\n";
        }

        $content .= '</ul>';

        return $content;
    }

    public function getContent(): ?string
    {
        $content = '';

        foreach ($this->items as $item) {
            /** @var ListItem $item */
            $content .= '- '.$item->getContent()."\n";
        }

        return $content;
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::LIST_BLOCK->value;
    }
}
