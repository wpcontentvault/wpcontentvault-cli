<?php

declare(strict_types=1);

namespace App\Console\Commands\Migrate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V1\ManifestReader;
use App\Services\Vault\Manifest\V2\ManifestWriter;

class MigrateToManifestV2Command extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-v2 {--year=} {--continue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate article manifest to v2';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository    $articles,
        ManifestReader       $manifestReader,
        ManifestWriter       $manifestWriter,
        ManifestNameResolver $manifestNameResolver
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

            foreach ($article->localizations as $localization) {
                $name = $manifestNameResolver->resolveName($article, $localization->locale);

                try {
                    $meta = $manifestReader->loadManifestFromPath($article->path, $name);

                    $manifestWriter->writeManifest($article->path, $name, $meta);
                }catch (\AssertionError $exception){
                    $this->info("Article already migrated!");
                }
            }
        }

        return self::SUCCESS;
    }
}
