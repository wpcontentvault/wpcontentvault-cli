<?php

declare(strict_types=1);

namespace App\Console\Commands\Classification;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Classification\ArticleCategorizer;
use App\Services\Classification\ArticleTagger;

class SuggestTagsForAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suggest-tags-for-articles {--year=} {--continue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Determines tags for all articles using AI';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository  $articles,
        ArticleTagger $tagger,
    )
    {
        $year = $this->option('year');
        $continue = $this->option('continue');

        if (null === $year) {
            $articlesList = $articles->getAllArticles();
        } else {
            $articlesList = $articles->getArticlesByYear(intval($year));
        }

        foreach ($articlesList as $article) {
            if ($continue !== null && $article->external_id !== intval($continue)) {
                continue;
            } else {
                $continue = null;
            }

            $this->info("Updating article {$article->title} ({$article->external_id})");
            $tagger->updateTagsForArticle($article);
            $this->info("Done.");
        }

        return self::SUCCESS;
    }
}
