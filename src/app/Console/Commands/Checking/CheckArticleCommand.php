<?php

declare(strict_types=1);

namespace App\Console\Commands\Checking;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Checking\CheckingService;

class CheckArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-article {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check discovered article for errors';

    /**
     * Execute the console command.
     */
    public function handle(ArticleRepository $articles, CheckingService $service): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            throw new \RuntimeException("Article $id not found");
        }

        $service->checkArticle($article);

        return self::SUCCESS;
    }
}
