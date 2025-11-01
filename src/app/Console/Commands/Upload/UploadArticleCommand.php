<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Console\Commands\AbstractApplicationCommand;
use App\Events\ArticleBeforeExportEvent;
use App\Repositories\ArticleRepository;
use App\Services\Exporting\ArticleExporter;
use App\Services\Exporting\PreviewExporter;
use App\Services\Importing\ArticleStatusImporter;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Wordpress\LocalizationBindingUpdater;
use App\Services\Wordpress\PostMetaUpdater;
use Illuminate\Events\Dispatcher;
use RuntimeException;

class UploadArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-article {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload the specified article to remote website';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ArticleExporter $exporter,
        ManifestNameResolver $manifestNameResolver,
        PreviewExporter $coverImporter,
        PostMetaUpdater $postMetaUpdater,
        LocalizationBindingUpdater $localizationBindingUpdater,
        ArticleStatusImporter $statusImporter,
    ): int {
        $id = $this->argument('id');

        $article = $articles->findArticleByExternalId(intval($id));

        if ($article === null) {
            throw new RuntimeException("Article $id not found");
        }

        foreach ($article->localizations as $localization) {
            $statusImporter->pullArticleStatus($article, $localization->locale);

            $name = $manifestNameResolver->resolveName($article, $localization->locale);

            $exporter->exportLocalizationFromDir($article->path, $name);

            $coverImporter->setCover($article->path, $name);

            $postMetaUpdater->updateTitleAndCategory($article->path, $name);
            $postMetaUpdater->updateTags($article->path, $name);
        }

        $localizationBindingUpdater->updateLocalizationBindingsForArticle($article);

        return self::SUCCESS;
    }
}
