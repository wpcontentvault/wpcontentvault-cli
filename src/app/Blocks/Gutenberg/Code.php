<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;

class Code extends GutenbergBlock
{
    public function __construct(?string $content)
    {
        parent::__construct($content);
    }

    public function render(): array
    {
        return [
            'blockName' => 'core/code',
            'attrs' => [
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
        $content = rtrim($this->content, "\n");

        return "\n<pre class=\"wp-block-code\"><code>$content</code></pre>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::CODE->value;
    }
}
