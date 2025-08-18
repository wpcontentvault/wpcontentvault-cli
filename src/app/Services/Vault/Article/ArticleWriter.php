<?php

declare(strict_types=1);

namespace App\Services\Vault\Article;

use App\Context\Markdown\PostMeta;
use App\Services\Importing\ImageDownloader;
use App\Services\Vault\Manifest\V2\ManifestWriter;
use App\Services\Vault\Meta\ImportChecksumMeta;
use RuntimeException;

class ArticleWriter
{
    public function __construct(
        private ManifestWriter $manifestWriter,
        private ImageDownloader $imageDownloader,
        private ImportChecksumMeta $importChecksumMeta,
    ) {}

    public function writeArticle(string $path, string $name, string $content, PostMeta $meta): void
    {
        file_put_contents($path.'/'.$name.'.md', $content);

        $this->manifestWriter->writeManifest($path, $name, $meta);
    }

    public function writeCover(string $path, string $name, string $url): void
    {
        $coverPath = $path.'/cover';
        if (file_exists($coverPath) === false) {
            mkdir($coverPath);
        }

        $this->imageDownloader->downloadPreview($url, $coverPath, $name);
    }

    public function protectFromRewrite(string $path, string $name): void
    {
        $sumFile = $this->importChecksumMeta->resolveImportChecksumFilePath($path, $name);

        $articleFile = $path.'/'.$name.'.md';
        if (file_exists($sumFile)) {
            $sumLastModified = filemtime($sumFile);
            $articleLastModified = filemtime($articleFile);

            $difference = abs($sumLastModified - $articleLastModified);
            // difference larger than 10 seconds
            if ($difference > 10) {
                throw new RuntimeException("Article was modified manually! Time difference is $difference.");
            }
        }
    }
}
