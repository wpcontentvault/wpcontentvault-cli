<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Events\ArticleDiscovered;
use App\Services\Console\ApplicationOutput;
use App\Services\Vault\Article\ArticleReader;
use App\Services\Vault\Meta\ArticleIdMeta;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Events\Dispatcher;
use Symfony\Component\Finder\Finder;

class DiscoverArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discover-articles {--update} {--replace} {--do-not-upload} {--offset=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover and load to the database all articles from vault';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleReader $reader,
        GlobalConfiguration $config,
        VaultPathResolver $pathResolver,
        ArticleIdMeta $articleIdMeta,
        ApplicationOutput $output,
        Dispatcher $eventDispatcher
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
        $offset = intval($this->option('offset') ?? 0);

        $finder = new Finder;
        $finder->name('*.*');
        $finder->sortByName();

        $count = 0;
        foreach ($finder->directories()->in($pathResolver->getRoot()) as $dir) {
            $count++;
            if ($count < $offset) {
                $output->info('Skipping: '.$dir);

                continue;
            }

            $output->info("#$count, found: $dir");

            $path = $dir->getPathname().'/';

            $article = $reader->loadArticleFromPath($path);

            $articleIdMeta->writeSerializedId($path, $article->getKey());

            $this->info("Article with {$article->external_id} found or created.");

            $eventDispatcher->dispatch(new ArticleDiscovered(intval($article->external_id), $path));
        }

        return self::SUCCESS;
    }
}
