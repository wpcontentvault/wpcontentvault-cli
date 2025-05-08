<?php

declare(strict_types=1);

namespace App\Console\Commands\Pull;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Importing\ArticleStatusImporter;

class PullMetaForAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull-meta-for-all-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull meta for all discovered articles';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository     $articles,
        ArticleStatusImporter $importer,
    ): int
    {
        $list = $articles->getAllArticles();

        foreach ($list as $article) {
            foreach ($article->localizations as $localization) {
                $importer->pullArticleStatus($article, $localization->locale);
            }
        }

        return self::SUCCESS;
    }
}
