<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\ArticleRepository;

class FindArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find {query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find article by title';

    /**
     * Execute the console command.
     */

    public function handle(ArticleRepository $articles): int
    {
        $query = $this->argument('query');

        $list = $articles->searchArticles($query);

        if (count($list) === 0) {
            $this->info("No articles found for query '{$query}'");

            return self::FAILURE;
        }

        foreach ($list as $article) {
            $this->info($article->title . " ({$article->external_id})");
        }

        return self::SUCCESS;
    }
}
