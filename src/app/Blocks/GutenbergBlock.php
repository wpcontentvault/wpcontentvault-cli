<?php

declare(strict_types=1);

namespace App\Blocks;

use App\Configuration\WordpressConfiguration;

abstract class GutenbergBlock
{
    protected ?string $content = null;

    public function __construct(?string $content)
    {
        if ($content !== null) {
            $content = str_replace('&quot;', '"', $content);
        }

        $this->content = $content;
    }

    abstract public function getHTML(WordpressConfiguration $configuration): string;

    abstract public function render(WordpressConfiguration $configuration): array;

    abstract public function getSlug(): string;

    public function getContent(): ?string
    {
        return $this->content;
    }
}
