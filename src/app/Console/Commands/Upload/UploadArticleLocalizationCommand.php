<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Exporting\ArticleExporter;
use App\Services\Vault\Manifest\ManifestNameResolver;
use RuntimeException;

class UploadArticleLocalizationCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-article-localization {id} {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload to remote website a specific locale for the article';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        LocaleRepository $locales,
        ArticleExporter $exporter,
        ManifestNameResolver $manifestNameResolver
    ): void {
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

        $exporter->exportLocalizationFromDir($article->path, $name);
    }
}
