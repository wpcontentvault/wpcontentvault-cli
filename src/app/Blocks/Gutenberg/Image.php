<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;

class Image extends GutenbergBlock
{
    private string $src;

    private string $alt;

    private ?int $externalId = null;

    public function __construct(string $src, string $alt, ?int $externalId)
    {
        parent::__construct(null);

        $this->src = $src;
        $this->alt = $alt;
        $this->externalId = $externalId;
    }

    public function render(): array
    {
        assert(empty($this->externalId) === false, $this->src);

        return [
            'blockName' => 'core/image',
            'attrs' => [
                'lightbox' => [
                    'enabled' => true,
                ],
                'id' => intval($this->externalId),
                'sizeSlug' => 'large',
                'linkDestination' => 'none',
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
        return "\n<figure class=\"wp-block-image size-large\"><img src=\"{$this->src}\" alt=\"{$this->alt}\" class=\"wp-image-{$this->externalId}\"/></figure>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::IMAGE->value;
    }
}
