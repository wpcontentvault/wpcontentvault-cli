<?php

declare(strict_types=1);

namespace App\Blocks;

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

    abstract public function getHTML(): string;

    abstract public function render(): array;

    abstract public function getSlug(): string;

    public function getContent(): ?string
    {
        return $this->content;
    }
}
