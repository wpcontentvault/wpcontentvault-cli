<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Exporting\ArticleExporter;
use App\Services\Exporting\PreviewExporter;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Wordpress\LocalizationBindingUpdater;
use App\Services\Wordpress\PostMetaUpdater;

class UploadAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-articles {--year=?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload all discovered articles to remote website';

    public function handle(
        ArticleRepository          $articles,
        ArticleExporter            $exporter,
        ManifestNameResolver       $manifestNameResolver,
        PreviewExporter            $coverImporter,
        PostMetaUpdater            $postMetaUpdater,
        LocalizationBindingUpdater $localizationBindingUpdater,
        ApplicationOutput          $output,
    ): int
    {
        $year = $this->option('year');

        if (null === $year) {
            $articlesList = $articles->getAllArticles();
        } else {
            $articlesList = $articles->getArticlesByYear(intval($year));
        }

        $this->info("Found " . count($articlesList) . " articles");

        foreach ($articlesList as $article) {
            $output->info("Uploading {$article->title} ({$article->external_id})");

            foreach ($article->localizations as $localization) {
                $name = $manifestNameResolver->resolveName($article, $localization->locale);

                $exporter->exportLocalizationFromDir($article->path, $name);

                $coverImporter->setCover($article->path, $name);

                $postMetaUpdater->updateTitleAndCategory($article->path, $name);
            }

            $localizationBindingUpdater->updateLocalizationBindingsForArticle($article);

            $output->info(str_repeat('=', 24));
        }

        return self::SUCCESS;
    }
}
