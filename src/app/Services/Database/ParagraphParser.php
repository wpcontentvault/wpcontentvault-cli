<?php

declare(strict_types=1);

namespace App\Services\Database;

use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Models\Article;
use App\Repositories\ParagraphRepository;
use App\Services\Converters\HTMLToMarkdown\HtmlToMarkdownConverter;
use App\Services\Converters\ObjectToGutenberg\ObjectToGutenbergConverter;
use App\Services\Database\Cleaner\ParagraphCleaner;
use App\Services\Database\Deserializer\ArticleBlocksDeserializer;
use App\Services\Database\Hasher\ParagraphHasher;
use App\Services\Importing\NopImageDownloader;
use RuntimeException;

class ParagraphParser
{
    public function __construct(
        private ObjectToGutenbergConverter $objectToGutenbergConverter,
        private ArticleBlocksDeserializer  $deserializer,
        private ParagraphHasher            $paragraphHasher,
        private ParagraphCleaner           $paragraphCleaner,
        private ParagraphRepository        $paragraphs,
        private WordpressConfiguration     $configuration,
    ) {}

    public function parse(Article $article): void
    {
        $oldParagraphIds = $this->paragraphs->findParagraphsByArticle($article)
            ->pluck('id')->toArray();
        $article->paragraph_ids->clear();

        $converter = new HtmlToMarkdownConverter($article->path, new NopImageDownloader);

        $blocks = $this->deserializer->deserialize($article);

        $gutenbergBlocks = $this->objectToGutenbergConverter->convert($blocks);

        $usedHashes = [];

        $prevBlockHash = '';

        /** @var GutenbergBlock $block */
        foreach ($gutenbergBlocks as $index => $block) {
            $html = "<html>{$block->getHTML($this->configuration)}</html>";
            $content = $converter->convert($html);

            $hash = $this->paragraphHasher->getHash($content, $block->getSlug(), $prevBlockHash);
            // When we have two same paragraphs with same hash the second paragraph will rewrite first!
            // Try generate hash with position to make it unique
            if (in_array($hash, $usedHashes) && empty($content) === false) {
                $hash = $this->paragraphHasher->getHashWithPosition($content, $block->getSlug(), $index);
            }

            if (in_array($hash, $usedHashes) && empty($content) === false) {
                throw new RuntimeException("Duplicate paragraph hash $hash, may cause problems! Content: $content");
            }

            $prevBlockHash = $hash;


            $paragraph = $this->paragraphs->findParagraphByHashAndArticle($hash, $article);

            if ($paragraph === null) {
                $paragraph = $this->paragraphs->createModel();
                $paragraph->article()->associate($article);
                $paragraph->content = $content;
                $paragraph->hash = $hash;
                $paragraph->order = $index;
                $paragraph->type = $block->getSlug();
                $paragraph->save();
            } else {
                $paragraph->order = $index;
                $paragraph->save();
            }

            $article->paragraph_ids->add($paragraph->getKey());

            $usedHashes[] = $hash;
        }

        $article->save();

        $newParagraphIds = $article->paragraph_ids->toArray();

        $removedParagraphs = array_diff($oldParagraphIds, $newParagraphIds);

        $this->paragraphCleaner->markParagraphsAsStale($removedParagraphs);
    }
}
