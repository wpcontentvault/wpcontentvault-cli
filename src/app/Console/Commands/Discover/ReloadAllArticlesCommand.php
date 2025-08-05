<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Vault\Article\ArticleReader;

class ReloadAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload-articles-from-disk {--update} {--replace} {--do-not-upload} {--year=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reloads already discovered articles from vault';

    public function handle(
        ArticleReader $loader,
        GlobalConfiguration $config,
        ArticleRepository $articles
    ): int {
        $replaceImages = $this->option('replace') ?? false;
        if ($replaceImages) {
            $config->replaceImages();
        }
        $updateImages = $this->option('update') ?? false;
        if ($updateImages) {
            $config->updateImages();
        }
        $doNotUploadImages = $this->option('do-not-upload') ?? false;
        if ($doNotUploadImages) {
            $config->throwOnImageUpload();
        }

        $year = $this->option('year');

        if (null === $year) {
            $list = $articles->getAllArticles();
        } else {
            $list = $articles->getArticlesByYear(intval($year));
        }

        foreach ($list as $article) {
            $loaded = $loader->loadArticleFromPath($article->path);

            file_put_contents($loaded->path.'article_id.txt', $article->getKey());
        }

        return self::SUCCESS;
    }
}
