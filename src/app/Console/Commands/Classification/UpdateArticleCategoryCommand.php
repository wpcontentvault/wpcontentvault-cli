<?php

declare(strict_types=1);

namespace App\Console\Commands\Classification;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Classification\ArticleCategorizer;

class UpdateArticleCategoryCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-category {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Determines article category using AI';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ArticleCategorizer $categorizer,
    ) {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article $id not found!");

            return self::FAILURE;
        }

        $categorizer->updateCategoryForArticle($article);

        return self::SUCCESS;
    }
}
