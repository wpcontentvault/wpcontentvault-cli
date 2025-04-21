<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Exporting\PreviewExporter;
use App\Services\Vault\Manifest\ManifestNameResolver;
use RuntimeException;

class UploadArticleThumbnailCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-article-thumbnail {id} {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        PreviewExporter $previewExporter,
        ArticleRepository $articles,
        LocaleRepository $locales,
        ManifestNameResolver $manifestNameResolver,
    ): int {
        $id = intval($this->argument('id'));
        $localeCode = $this->argument('locale');

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new RuntimeException('Article not found!');
        }

        $locale = $locales->findLocaleByCode($localeCode);
        if ($locale === null) {
            throw new RuntimeException('Locale not found!');
        }

        $name = $manifestNameResolver->resolveName($article, $locale);

        $previewExporter->setCover($article->path, $name);

        return self::SUCCESS;
    }
}
