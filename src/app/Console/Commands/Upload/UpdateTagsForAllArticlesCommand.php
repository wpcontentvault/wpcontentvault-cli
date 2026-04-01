<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Article;
use App\Models\ArticleLocalization;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Wordpress\PostMetaUpdater;

class UpdateTagsForAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-tags-for-articles {--year=} {--continue=} {--tag=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates article tags for all articles';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository    $articles,
        ManifestNameResolver $manifestNameResolver,
        PostMetaUpdater      $postMetaUpdater,
        TagRepository        $tags
    ): int
    {
        $year = $this->option('year');
        $continue = $this->option('continue');
        $tagSlug = $this->option('tag');

        if (null === $year) {
            $articlesList = $articles->getAllArticles();
        } else {
            $articlesList = $articles->getArticlesByYear(intval($year));
        }

        if (null !== $tagSlug) {
            $tag = $tags->findTagBySlug($tagSlug);
        } else {
            $tag = null;
        }

        foreach ($articlesList as $article) {
            /** @var Article $article */
            if ($continue !== null && $article->external_id !== intval($continue)) {
                continue;
            } else {
                $continue = null;
            }

            if(null !== $tag && false === $article->findTagBySlug($tag->slug)){
                continue;
            }

            foreach ($article->localizations as $localization) {
                /** @var ArticleLocalization $localization */
                $name = $manifestNameResolver->resolveName($article, $localization->locale);

                $postMetaUpdater->updateTags($article->path, $name);
            }
        }
        return self::SUCCESS;
    }
}
