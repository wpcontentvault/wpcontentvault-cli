<?php

declare(strict_types=1);

namespace App\Services\Converters\HTMLToMarkdown\Converter;

use App\Contracts\Wordpress\ImageDownloaderInterface;
use App\Services\Utils\StringUtils;
use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

class ImageConverter implements ConverterInterface
{
    public function __construct(
        private ImageDownloaderInterface $downloader,
        private string $path
    ) {}

    public function convert(ElementInterface $element): string
    {
        $src = $element->getAttribute('src');

        if ($element->getTagName() === 'img' || $this->isDownloadableVideo($element)) {
            $src = $this->downloader->downloadMedia($src, $this->path);
        }

        $alt = $element->getAttribute('alt');
        if (StringUtils::containsCyrillic($alt) === false) {
            $alt = '';
        }

        $title = $element->getAttribute('title');

        if ($title !== '') {
            // No newlines added. <img> should be in a block-level element.
            return '!['.$alt.']('.$src.' "'.$title.'")'."\n\n";
        }

        return '!['.$alt.']('.$src.')'."\n\n";
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['img', 'video'];
    }

    private function isDownloadableVideo(ElementInterface $element): bool
    {
        $tagName = $element->getTagName();
        $src = $element->getAttribute('src');

        return $tagName === 'video' && str_starts_with($src, 'https://losst');
    }
}
