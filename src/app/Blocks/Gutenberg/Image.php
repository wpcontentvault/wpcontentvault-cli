<?php

declare(strict_types=1);

namespace App\Blocks\Gutenberg;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Enum\GutenbergBlogTypeEnum;
use App\Enum\Wordpress\ImageAlignEnum;
use App\Enum\Wordpress\ImageLinkDestinationEnum;
use Doctrine\Inflector\Rules\Word;

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

    public function render(WordpressConfiguration $configuration): array
    {
        assert(empty($this->externalId) === false, $this->src);

        return [
            'blockName' => 'core/image',
            'attrs' => [
                'lightbox' => [
                    'enabled' => $configuration->isImageLightboxEnabled(),
                ],
                'id' => intval($this->externalId),
                'sizeSlug' => 'large',
                'linkDestination' => $configuration->getImageLinkDestination(),
                'align' => $configuration->getImageAlign(),
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
        $classList = ["wp-block-image"];

        if ($configuration->getImageAlign() !== ImageAlignEnum::NONE->value) {
            $classList[] = "align" . $configuration->getImageAlign();
        }
        $classList[] = 'size-large';

        $classString = implode(' ', $classList);

        $imgString = "<img src=\"{$this->src}\" alt=\"{$this->alt}\" class=\"wp-image-{$this->externalId}\"/>";

        if ($configuration->getImageLinkDestination() === ImageLinkDestinationEnum::MEDIA->value) {
            $imgString = "<a href=\"{$this->src}\">{$imgString}</a>";
        }

        return "\n<figure class=\"$classString\">$imgString</figure>\n";
    }

    public function getSlug(): string
    {
        return GutenbergBlogTypeEnum::IMAGE->value;
    }
}
