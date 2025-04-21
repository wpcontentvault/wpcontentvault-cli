<?php

declare(strict_types=1);

namespace App\Services\Importing;

use App\Context\Markdown\PostMeta;
use App\Contracts\Wordpress\ImageDownloaderInterface;
use App\Events\ArticleImported;
use App\Models\Article;
use App\Models\Locale;
use App\Services\Converters\HTMLToMarkdown\HtmlToMarkdownConverter;
use App\Services\Database\Resolver\CategoryResolver;
use App\Services\Importing\Checksum\Checksum;
use App\Services\Vault\Article\ArticleWriter;
use App\Services\Vault\Meta\ImportChecksumMeta;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Events\Dispatcher;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\FullPostData;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class ArticleImporter
{
    private ImageDownloaderInterface $downloader;

    public function __construct(
        private ArticleWriter $writer,
        private PathResolver $pathResolver,
        private CategoryResolver $categoryResolver,
        private Dispatcher $eventDispatcher,
        private ImportChecksumMeta $importChecksumMeta,
    ) {
        $this->downloader = new ImageDownloader;
    }

    public function importArticle(int $postId, WPConnectorInterface $connector, Locale $locale, string $name): void
    {
        $this->processArticle($postId, $connector, $locale, $name, function (FullPostData $postData) {
            return $this->pathResolver->resolvePathFromPostData($postData);
        });
    }

    public function importTranslation(Article $article, int $postId, WPConnectorInterface $connector, Locale $locale, string $name): void
    {
        $this->processArticle($postId, $connector, $locale, $name, function (FullPostData $postData) use ($article) {
            return $article->path;
        });
    }

    public function importCover(int $postId, WPConnectorInterface $connector, string $name): void
    {
        $this->processImportCover($postId, $connector, $name, function (FullPostData $postData) {
            return $this->pathResolver->resolvePathFromPostData($postData);
        });
    }

    public function importTranslationCover(Article $article, int $postId, WPConnectorInterface $connector, string $name): void
    {
        $this->processImportCover($postId, $connector, $name, function (FullPostData $postData) use ($article) {
            return $article->path;
        });
    }

    private function processArticle(int $postId, WPConnectorInterface $connector, Locale $locale, string $name, Closure $getPath): void
    {
        $postData = $connector->getPost($postId);

        $thumbnail = $connector->getPostThumbnail($postId);

        $postContent = $postData->content;
        $postContent = str_replace("'", "'", $postContent);

        $sum = Checksum::calculate($postContent);

        $path = $getPath($postData);

        if (file_exists($path) === false) {
            mkdir($path);
        }

        $converter = new HtmlToMarkdownConverter($path, $this->downloader);

        $converted = $converter->convert($postContent);

        $converted = str_replace('``` ```', "```\n\n```", $converted);
        $converted = str_replace('``` ![', "```\n\n![", $converted);
        if (str_ends_with($converted, "\n") === false) {
            $converted .= "\n";
        }

        $category = $this->categoryResolver->resolveCategoryByName($postData->category, $locale);

        $meta = new PostMeta(
            locale: $locale,
            title: $postData->title,
            status: $postData->status,
            author: $postData->author,
            publishedAt: new CarbonImmutable($postData->publishedAt),
            modifiedAt: new CarbonImmutable($postData->modifiedAt),
            url: $postData->url,
            externalId: $postData->id,
            category: $category,
            tags: $postData->tags
        );

        $this->writer->protectFromRewrite($path, $name);

        $this->writer->writeArticle($path, $name, $converted, $meta);

        if (empty($thumbnail->attachmentUrl) === false) {
            $this->writer->writeCover($path, $name, $thumbnail->attachmentUrl);
        }

        $this->importChecksumMeta->writeImportChecksum($path, $name, $sum);

        $this->eventDispatcher->dispatch(new ArticleImported($postId, $path, $connector, $locale));
    }

    private function processImportCover(int $postId, WPConnectorInterface $connector, string $fileName, Closure $getPath): void
    {
        $postData = $connector->getPost($postId);
        $thumbnail = $connector->getPostThumbnail($postId);

        $this->writer->writeCover(
            $getPath($postData),
            $fileName,
            $thumbnail->attachmentUrl,
        );
    }
}
