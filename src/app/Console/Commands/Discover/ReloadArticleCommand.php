<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Vault\Article\ArticleReader;

class ReloadArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload-article {id} {--update} {--replace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reloads already discovered article';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleReader $loader,
        GlobalConfiguration $config,
        ArticleRepository $articles
    ): int {
        $id = intval($this->argument('id'));

        $replaceImages = $this->option('replace') ?? false;
        if ($replaceImages) {
            $config->replaceImages();
        }
        $updateImages = $this->option('update') ?? false;
        if ($updateImages) {
            $config->updateImages();
        }

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article $id not found!");

            return self::FAILURE;
        }

        $loaded = $loader->loadArticleFromPath($article->path);

        file_put_contents($loaded->path.'article_id.txt', $article->getKey());

        return self::SUCCESS;
    }
}
