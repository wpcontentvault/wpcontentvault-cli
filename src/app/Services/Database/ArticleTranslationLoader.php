<?php

declare(strict_types=1);

namespace App\Services\Database;

use App\Blocks\Gutenberg\Image;
use App\Blocks\Gutenberg\Separator;
use App\Blocks\GutenbergBlock;
use App\Enum\GutenbergBlogTypeEnum;
use App\Exceptions\Translation\TranslationLoadingException;
use App\Exceptions\Translation\TranslationMatchingException;
use App\Models\Article;
use App\Models\Locale;
use App\Models\Paragraph;
use App\Repositories\ParagraphRepository;
use App\Services\Converters\HTMLToMarkdown\HtmlToMarkdownConverter;
use App\Services\Converters\ObjectToGutenberg\ObjectToGutenbergConverter;
use App\Services\Importing\NopImageDownloader;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\MarkdownLoader;

class ArticleTranslationLoader
{
    public function __construct(
        private ManifestNameResolver $manifestNameResolver,
        private MarkdownLoader $markdownLoader,
        private ParagraphRepository $paragraphRepository,
        private ObjectToGutenbergConverter $objectToGutenbergConverter,
    ) {}

    public function fetchAllTranslationsFromStorage(Article $article, Locale $locale): array
    {
        $name = $this->manifestNameResolver->resolveName($article, $locale);

        $converter = new HtmlToMarkdownConverter($article->path, new NopImageDownloader);

        $blocks = $this->markdownLoader->loadBlocksFromPath($article->path, $name);

        $gutenbergBlocks = $this->objectToGutenbergConverter->convert($blocks);

        $paragraphs = $this->paragraphRepository->findParagraphsByArticle($article);
        // Remove separators
        $paragraphs = $paragraphs->filter(function (Paragraph $paragraph) {
            return $paragraph->type !== GutenbergBlogTypeEnum::SEPARATOR->value;
        })->values();

        $gutenbergBlocks = $gutenbergBlocks->filter(function (GutenbergBlock $block) {
            return false === ($block instanceof Separator);
        });

        if (count($paragraphs) === 0) {
            throw new TranslationLoadingException(
                'Paragraphs not loaded yet!'
            );
        }

        if (count($paragraphs) !== count($gutenbergBlocks)) {
            throw new TranslationLoadingException(
                'Number of original paragraphs and translated does not match!'
            );
        }

        $translations = [];

        $index = 0;

        foreach ($gutenbergBlocks as $block) {
            /** @var GutenbergBlock $block */
            if ($block instanceof Separator) {
                continue;
            }

            $html = "<html>{$block->getHTML()}</html>";
            $content = $converter->convert($html);

            /** @var Paragraph|null $paragraph */
            $paragraph = $paragraphs->get($index);

            if ($paragraph === null) {
                throw new TranslationMatchingException(
                    "Paragraph with id {$index} not found",
                    null,
                    $block->getSlug(),
                    null,
                    $content
                );
            }

            if ($block->getSlug() !== $paragraph->type) {
                throw new TranslationMatchingException(
                    'Paragraph type mismatch! Synchronization is not possible.',
                    $paragraph->type,
                    $block->getSlug(),
                    $paragraph->content,
                    $content
                );
            }

            if ($block instanceof Image && $content !== $paragraph->content) {
                throw new TranslationMatchingException(
                    'Image src mismatch! Synchronization is not possible.',
                    $paragraph->type,
                    $block->getSlug(),
                    $paragraph->content,
                    $content
                );
            }

            $translations[$paragraph->getKey()] = $content;

            $index++;
        }

        return $translations;
    }
}
