<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Events\ArticleDiscovered;
use App\Services\Vault\Article\ArticleReader;
use App\Services\Vault\Meta\ArticleIdMeta;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Events\Dispatcher;

class DiscoverArticleFromPathCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discover-article-from-path {path} {--update} {--replace} {--do-not-upload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover and load article from path to the database';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleReader $reader,
        GlobalConfiguration $config,
        VaultPathResolver $pathResolver,
        ArticleIdMeta $articleIdMeta,
        Dispatcher $eventDispatcher,
    ): int {
        $replaceImages = $this->option('replace') ?? false;
        if ($replaceImages) {
            $config->replaceImages();
        }
        $updateImages = $this->option('update') ?? false;
        if ($updateImages) {
            $config->updateImages();
        }
        $updateImages = $this->option('do-not-upload') ?? false;
        if ($updateImages) {
            $config->throwOnImageUpload();
        }

        $path = $this->argument('path');

        if (empty($path)) {
            $this->error('Path is empty!');

            return self::FAILURE;
        }

        $path = $pathResolver->resolveArticlePath($path);

        $article = $reader->loadArticleFromPath($path);

        $articleIdMeta->writeSerializedId($path, $article->getKey());

        $eventDispatcher->dispatch(new ArticleDiscovered(intval($article->external_id), $path));

        $this->info("Article with {$article->external_id} found or created.");

        return self::SUCCESS;
    }
}
