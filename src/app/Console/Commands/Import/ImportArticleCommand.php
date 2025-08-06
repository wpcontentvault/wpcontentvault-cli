<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Console\Commands\AbstractApplicationCommand;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use App\Services\Importing\ArticleImporter;

class ImportArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-article {id} {--locale=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import article to the vault from WordPress using its external id';

    /**
     * Execute the console command.
     */
    public function handle(SitesRegistry $sitesConfiguration, LocaleRepository $locales, ArticleImporter $importer): int
    {
        $id = intval($this->argument('id'));

        $localeCode = $this->option('locale');
        if (empty($localeCode)) {
            $localeCode = $sitesConfiguration->getMainSiteLocaleCode();
        }

        $locale = $locales->findLocaleByCode($localeCode);

        if(false === $sitesConfiguration->hasSiteConnectorForLocale($locale)){
            $this->output->error("No connector configured for $localeCode");

            return self::FAILURE;
        }

        $connector = $sitesConfiguration->getSiteConnectorByLocale($locale);

        $importer->importArticle($id, $connector, $locale, 'original');

        return self::SUCCESS;
    }
}
