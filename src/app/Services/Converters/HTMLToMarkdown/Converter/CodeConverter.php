<?php

declare(strict_types=1);

namespace App\Services\Converters\HTMLToMarkdown\Converter;

use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

class CodeConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        $code = \html_entity_decode($element->getChildrenAsString());

        // In order to remove the code tags we need to search for them and, in the case of the opening tag
        // use a regular expression to find the tag and the other attributes it might have
        $code = \preg_replace('/<code\b[^>]*>/', '', $code);
        \assert($code !== null);
        $code = \str_replace('</code>', '', $code);

        $code = \str_replace('<br/>', "\n", $code);
        $code = \str_replace('<br></br>', "\n", $code);
        if (str_starts_with($code, ' ')) {
            $code = substr($code, 1);
        }

        // Gutenberg blocks
        $code = rtrim($code, "\n");

        return '```'."\n".$code."\n".'``` ';
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['code'];
    }
}
