<?php

declare(strict_types=1);

namespace App\Console\Commands\Pull;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Importing\ArticleStatusImporter;

class PullArticleMetaCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull-article-meta {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull article published_at and modified_at dates from WordPress using its external id';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository     $articles,
        ArticleStatusImporter $importer,
    ): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article with id {$id} not found");

            return self::FAILURE;
        }

        foreach($article->localizations as $localization) {
            $importer->pullArticleStatus($article, $localization->locale);
        }

        return self::SUCCESS;
    }
}
