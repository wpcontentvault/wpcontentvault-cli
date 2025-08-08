<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;
use Doctrine\Inflector\Rules\Word;
use Illuminate\Support\Collection;

class Quote extends GutenbergBlock
{
    private Collection $items;

    public function __construct(?string $content, Collection $items)
    {
        parent::__construct($content);

        $this->items = $items;
    }

    public function render(WordpressConfiguration $configuration): array
    {
        $innerBlocks = [];

        foreach ($this->items as $item) {
            $innerBlocks[] = $item->render($configuration);
        }

        return [
            'blockName' => 'core/quote',
            'attrs' => [
            ],
            'innerBlocks' => $innerBlocks,
            'innerHTML' => '\n<blockquote class=\"wp-block-quote\"></blockquote>\n',
            'innerContent' => $this->getInnerContent(),
        ];
    }

    public function getInnerContent(): array
    {
        return [
            "\n<blockquote class=\"wp-block-quote\">",
            null,
            "</blockquote>\n",
        ];
    }

    public function getHTML(WordpressConfiguration $configuration): string
    {
        $content = "\n<blockquote class=\"wp-block-quote\">";

        foreach ($this->items as $item) {
            /** @var GutenbergBlock $item */
            $content .= $item->getHTML($configuration);
        }

        $content .= "</blockquote>\n";

        return $content;
    }

    public function getContent(): ?string
    {
        $content = '';

        foreach ($this->items as $item) {
            $content .= $item->getContent() . "\n";
        }

        return $content;
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::QUOTE->value;
    }
}
