<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Article;
use App\Models\ArticleLocalization;
use App\Repositories\ArticleRepository;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Wordpress\PostMetaUpdater;

class UpdateCategoriesForAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-categories-for-articles {--year=} {--continue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates article category for all articles';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository    $articles,
        ManifestNameResolver $manifestNameResolver,
        PostMetaUpdater      $postMetaUpdater,
    ): int
    {
        $year = $this->option('year');
        $continue = $this->option('continue');

        if (null === $year) {
            $articlesList = $articles->getAllArticles();
        } else {
            $articlesList = $articles->getArticlesByYear(intval($year));
        }

        foreach ($articlesList as $article) {
            /** @var Article $article */
            if ($continue !== null && $article->external_id !== intval($continue)) {
                continue;
            } else {
                $continue = null;
            }

            foreach($article->localizations as $localization) {
                /** @var ArticleLocalization $localization */
                $name = $manifestNameResolver->resolveName($article, $localization->locale);

                $postMetaUpdater->updateTitleAndCategory($article->path, $name);
            }
        }
        return self::SUCCESS;
    }
}
