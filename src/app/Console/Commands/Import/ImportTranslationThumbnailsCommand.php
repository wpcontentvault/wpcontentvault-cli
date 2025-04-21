<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Console\Commands\AbstractApplicationCommand;
use App\Registry\SitesRegistry;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Importing\ArticleImporter;
use RuntimeException;

class ImportTranslationThumbnailsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-translation-thumbnails {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import thumbnails for imported translations';

    /**
     * Execute the console command.
     */
    public function handle(
        SitesRegistry $sitesConfiguration,
        ArticleRepository $articles,
        LocaleRepository $locales,
        ArticleImporter $importer
    ): void {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            throw new RuntimeException("Article with $id not found!");
        }

        $mainConnector = $sitesConfiguration->getMainSiteConnector();

        $translations = $mainConnector->getTranslations(intval($id));

        foreach ($translations as $localeCode => $postId) {
            $locale = $locales->findLocaleByCode($localeCode);
            if ($locale === null) {
                throw new RuntimeException("Locale $localeCode not found!");
            }

            $connector = $sitesConfiguration->getSiteConnectorByLocale($locale);

            $importer->importTranslationCover($article, $postId, $connector, $localeCode);
        }
    }
}
