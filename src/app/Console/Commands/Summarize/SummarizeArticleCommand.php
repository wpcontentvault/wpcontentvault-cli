<?php

declare(strict_types=1);

namespace App\Console\Commands\Summarize;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Summarization\ArticleSummarizer;

class SummarizeArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summarize-article {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Summarize article';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ArticleSummarizer $summarizer,
    ): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article with id {$id} not found");

            return self::FAILURE;
        }

        $summarizer->summarizeArticle($article);

        return self::SUCCESS;
    }
}
