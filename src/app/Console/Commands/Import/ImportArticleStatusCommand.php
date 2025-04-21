<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Importing\ArticleStatusImporter;
use RuntimeException;

class ImportArticleStatusCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-article-status {id} {--locale=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import article status from WordPress using its external id';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        LocaleRepository $locales,
        ArticleStatusImporter $importer
    ): int {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article with id {$id} not found");

            return self::FAILURE;
        }

        $localeCode = $this->option('locale');

        $locale = $locales->findLocaleByCode($localeCode);
        if ($locale === null) {
            throw new RuntimeException("Locale with id {$localeCode} not found");
        }

        $importer->pullArticleStatus($article, $locale);

        return self::SUCCESS;
    }
}
