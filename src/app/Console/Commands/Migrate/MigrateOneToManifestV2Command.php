<?php

declare(strict_types=1);

namespace App\Console\Commands\Migrate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V1\ManifestReader;
use App\Services\Vault\Manifest\V2\ManifestWriter;

class MigrateOneToManifestV2Command extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-one-v2 {id}';

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
    ): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article with id {$id} not found");

            return self::FAILURE;
        }

        foreach ($article->localizations as $localization) {
            $name = $manifestNameResolver->resolveName($article, $localization->locale);

            $meta = $manifestReader->loadManifestFromPath($article->path, $name);

            $manifestWriter->writeManifest($article->path, $name, $meta);
        }

        return self::SUCCESS;
    }
}
