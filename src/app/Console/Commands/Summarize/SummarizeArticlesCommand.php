<?php

declare(strict_types=1);

namespace App\Console\Commands\Summarize;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Summarization\ArticleSummarizer;

class SummarizeArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summarize-articles {--year=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Summarize all discovered articles';

    public function handle(
        ArticleRepository $articles,
        ArticleSummarizer $articleSummarizer,
        ApplicationOutput $output,
    ): int
    {
        $year = $this->option('year');

        if (null === $year) {
            $articlesList = $articles->getAllArticles();
        } else {
            $articlesList = $articles->getArticlesByYear(intval($year));
        }

        $this->info("Found " . count($articlesList) . " articles");

        foreach ($articlesList as $article) {
            $output->info("Summarizing {$article->title} ({$article->external_id})");

            $articleSummarizer->summarizeArticle($article);
        }

        return self::SUCCESS;
    }
}
