<?php

declare(strict_types=1);

namespace App\Console\Commands\Classification;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Classification\ArticleTagger;

class SuggestArticleTagsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suggest-tags {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Determines article tag using AI';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ArticleTagger $tagger,
    ) {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article $id not found!");

            return self::FAILURE;
        }

        $tagger->updateTagsForArticle($article);

        return self::SUCCESS;
    }
}
