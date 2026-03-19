<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Console\Commands\AbstractApplicationCommand;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use App\Services\Importing\ArticleImporter;
use App\Services\Vault\Article\ArticleReader;
use App\Services\Vault\Meta\ArticleIdMeta;
use App\Services\Vault\VaultPathResolver;

class ImportArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-article {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import article to the vault from WordPress using its external id';

    /**
     * Execute the console command.
     */
    public function handle(
        SitesRegistry    $sitesConfiguration,
        LocaleRepository $locales,
        ArticleImporter  $importer,
        ArticleReader    $reader,
        ArticleIdMeta    $articleIdMeta
    ): int
    {
        $id = intval($this->argument('id'));

        $connector = $sitesConfiguration->getMainSiteConnector();
        $locale = $locales->findLocaleByCode($sitesConfiguration->getMainSiteLocaleCode());

        $path = $importer->importArticle($id, $connector, $locale, 'original');

        $article = $reader->loadArticleFromPath($path);
        $articleIdMeta->writeSerializedId($path, $article->getKey());

        $this->call('import-article-translations', ['id' => $article->external_id]);

        return self::SUCCESS;
    }
}
