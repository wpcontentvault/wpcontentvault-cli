<?php

declare(strict_types=1);

namespace App\Console\Commands\Checking;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Checking\CheckingService;

class CheckAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check article for errors';

    /**
     * Execute the console command.
     */
    public function handle(ArticleRepository $articles, CheckingService $service): int
    {
        $list = $articles->getAllArticles();

        $errorCount = 0;

        foreach ($list as $article) {
            $errorCount += $service->checkArticle($article);
        }

        $this->output->info("Errors found: $errorCount");

        return self::SUCCESS;
    }
}
