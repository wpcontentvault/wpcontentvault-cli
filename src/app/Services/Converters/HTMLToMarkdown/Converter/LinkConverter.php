<?php

declare(strict_types=1);

namespace App\Services\Converters\HTMLToMarkdown\Converter;

use App\Services\Utils\LinkChecker;
use League\HTMLToMarkdown\Configuration;
use League\HTMLToMarkdown\ConfigurationAwareInterface;
use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

class LinkConverter implements ConfigurationAwareInterface, ConverterInterface
{
    /** @var Configuration */
    protected $config;

    public function setConfig(Configuration $config): void
    {
        $this->config = $config;
    }

    public function convert(ElementInterface $element): string
    {
        $href = $element->getAttribute('href');

        if (LinkChecker::isLinkValid($href) === false) {
            // TODO: implement broken links logging
        }

        $title = $element->getAttribute('title');
        $text = \trim($element->getValue(), " \t\n\r\0\x0B");

        if ($title !== '') {
            $markdown = '['.$text.']('.$href.' "'.$title.'")';
        } elseif ($href === $text && $this->isValidAutolink($href)) {
            $markdown = '<'.$href.'>';
        } elseif ($href === 'mailto:'.$text && $this->isValidEmail($text)) {
            $markdown = '<'.$text.'>';
        } else {
            if (\stristr($href, ' ')) {
                $href = '<'.$href.'>';
            }

            $markdown = '['.$text.']('.$href.')';
        }

        $image = $this->hasImage($element);
        if ($image !== null) {
            return $image;
        }

        if (! $href) {
            if ($this->shouldStrip()) {
                $markdown = $text;
            } else {
                $markdown = \html_entity_decode($element->getChildrenAsString());
            }
        }

        return $markdown;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['a'];
    }

    private function isValidAutolink(string $href): bool
    {
        $useAutolinks = $this->config->getOption('use_autolinks');

        return $useAutolinks && (\preg_match('/^[A-Za-z][A-Za-z0-9.+-]{1,31}:[^<>\x00-\x20]*/i', $href) === 1);
    }

    private function isValidEmail(string $email): bool
    {
        // Email validation is messy business, but this should cover most cases
        return \filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function shouldStrip(): bool
    {
        return \boolval($this->config->getOption('strip_placeholder_links') ?? false);
    }

    private function hasImage(ElementInterface $element): ?string
    {
        $hasImage = null;

        foreach ($element->getChildren() as $child) {
            /** @var ElementInterface $child */
            if (str_starts_with($child->getValue(), '!')) {
                return $child->getValue();
            }

            $hasImage = $this->hasImage($child);
        }

        return $hasImage;
    }
}
