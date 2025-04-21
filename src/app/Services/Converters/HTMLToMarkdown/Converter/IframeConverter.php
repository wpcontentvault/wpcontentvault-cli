<?php

declare(strict_types=1);

namespace App\Services\Converters\HTMLToMarkdown\Converter;

use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

class IframeConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        $src = $element->getAttribute('src');

        if (str_starts_with($src, '//')) {
            $src = 'https:'.$src;
        }

        return '['.$src.']('.$src.')';
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['iframe'];
    }
}
