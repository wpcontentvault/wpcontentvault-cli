<?php

declare(strict_types=1);

namespace App\Services\Converters\HTMLToMarkdown;

use App\Contracts\Wordpress\ImageDownloaderInterface;
use App\Services\Converters\HTMLToMarkdown\Converter\CodeConverter;
use App\Services\Converters\HTMLToMarkdown\Converter\DivConverter;
use App\Services\Converters\HTMLToMarkdown\Converter\IframeConverter;
use App\Services\Converters\HTMLToMarkdown\Converter\ImageConverter;
use App\Services\Converters\HTMLToMarkdown\Converter\LinkConverter;
use League\HTMLToMarkdown\Converter\BlockquoteConverter;
use League\HTMLToMarkdown\Converter\CommentConverter;
use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\Converter\EmphasisConverter;
use League\HTMLToMarkdown\Converter\HardBreakConverter;
use League\HTMLToMarkdown\Converter\HeaderConverter;
use League\HTMLToMarkdown\Converter\HorizontalRuleConverter;
use League\HTMLToMarkdown\Converter\ListBlockConverter;
use League\HTMLToMarkdown\Converter\ListItemConverter;
use League\HTMLToMarkdown\Converter\ParagraphConverter;
use League\HTMLToMarkdown\Converter\PreformattedConverter;
use League\HTMLToMarkdown\Converter\TableConverter;
use League\HTMLToMarkdown\Converter\TextConverter;
use League\HTMLToMarkdown\ElementInterface;
use League\HTMLToMarkdown\Environment;
use League\HTMLToMarkdown\HtmlConverter as BaseHtmlConverter;

class HtmlToMarkdownConverter extends BaseHtmlConverter
{
    private static array $overrides = [];

    public function __construct(string $path, ImageDownloaderInterface $downloader)
    {
        $environment = new Environment([
            'header_style' => 'atx', // Set to 'atx' to output H1 and H2 headers as # Header1 and ## Header2
            'suppress_errors' => true, // Set to false to show warnings when loading malformed HTML
            'strip_tags' => true, // Set to true to strip tags that don't have markdown equivalents. N.B. Strips tags, not their content. Useful to clean MS Word HTML output.
            'strip_placeholder_links' => true, // Set to true to remove <a> that doesn't have href.
            'bold_style' => '**', // DEPRECATED: Set to '__' if you prefer the underlined style
            'italic_style' => '*', // DEPRECATED: Set to '_' if you prefer the underlined style
            'remove_nodes' => 'script figcaption', // space-separated list of dom nodes that should be removed. example: 'meta style script'
            'hard_break' => true, // Set to true to turn <br> into `\n` instead of `  \n`
            'list_item_style' => '-', // Set the default character for each <li> in a <ul>. Can be '-', '*', or '+'
            'preserve_comments' => false, // Set to true to preserve comments, or set to an array of strings to preserve specific comments
            'use_autolinks' => false, // Set to true to use simple link syntax if possible. Will always use []() if set to false
            'table_pipe_escape' => '\|', // Replacement string for pipe characters inside markdown table cells
            'table_caption_side' => 'top', // Set to 'top' or 'bottom' to show <caption> content before or after table, null to suppress
        ]);

        $this->addConverterToEnvironment($environment, new BlockquoteConverter);
        $this->addConverterToEnvironment($environment, new CodeConverter);
        $this->addConverterToEnvironment($environment, new CommentConverter);
        $this->addConverterToEnvironment($environment, new DivConverter);
        $this->addConverterToEnvironment($environment, new EmphasisConverter);
        $this->addConverterToEnvironment($environment, new HardBreakConverter);
        $this->addConverterToEnvironment($environment, new HeaderConverter);
        $this->addConverterToEnvironment($environment, new HorizontalRuleConverter);
        $this->addConverterToEnvironment($environment, new ImageConverter($downloader, $path));
        $this->addConverterToEnvironment($environment, new LinkConverter);
        $this->addConverterToEnvironment($environment, new ListBlockConverter);
        $this->addConverterToEnvironment($environment, new ListItemConverter);
        $this->addConverterToEnvironment($environment, new ParagraphConverter);
        $this->addConverterToEnvironment($environment, new PreformattedConverter);
        $this->addConverterToEnvironment($environment, new TextConverter);
        $this->addConverterToEnvironment($environment, new TableConverter);
        $this->addConverterToEnvironment($environment, new IframeConverter);

        parent::__construct($environment);
    }

    public static function replace(ConverterInterface $converter): void
    {
        $key = md5(serialize($converter->getSupportedTags()));

        self::$overrides[$key] = $converter;
    }

    protected function convertToMarkdown(ElementInterface $element): string
    {
        $class = $element->getAttribute('class');

        // Skip converting span with file path for file block
        if (str_contains($class, 'file-content-path')) {
            return '';
        }

        return parent::convertToMarkdown($element);
    }

    private function addConverterToEnvironment(Environment $environment, ConverterInterface $converter): void
    {
        $key = md5(serialize($converter->getSupportedTags()));

        if (isset(self::$overrides[$key])) {
            $environment->addConverter(self::$overrides[$key]);
        }

        $environment->addConverter($converter);
    }
}
