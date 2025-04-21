<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Console\Commands\AbstractApplicationCommand;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use App\Services\Importing\ArticleImporter;

class ImportArticleThumbnailCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-article-thumbnail {id} {--locale=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import article thumbnail';

    /**
     * Execute the console command.
     */
    public function handle(SitesRegistry $sites, LocaleRepository $locales, ArticleImporter $importer): void
    {
        $id = intval($this->argument('id'));

        $localeCode = $this->option('locale');
        if (empty($localeCode)) {
            $localeCode = $sites->getMainSiteLocaleCode();
        }

        $locale = $locales->findLocaleByCode($localeCode);
        $connector = $sites->getSiteConnectorByLocale($locale);

        $importer->importCover($id, $connector, 'original');
    }
}
