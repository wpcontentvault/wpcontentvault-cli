<?php

declare(strict_types=1);

namespace App\Console\Commands\Classification;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Classification\ArticleCategorizer;
use App\Services\Vault\Manifest\V2\ManifestReader;

class SuggestCategoryForAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suggest-category-for-articles {--year=} {--continue=} {--skip-not-empty}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Determines category for all articles using AI';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository  $articles,
        ArticleCategorizer $categorizer,
        ManifestReader     $manifestReader,
    )
    {
        $year = $this->option('year');
        $continue = $this->option('continue');
        $skipNotEmpty = boolval($this->option('skip-not-empty'));

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

            if ($skipNotEmpty) {
                $meta = $manifestReader->loadManifestFromPath($article->path, 'original');

                if ($meta->category !== null) {
                    $this->info("Skipping article {$article->title} ({$article->external_id})");
                    continue;
                }
            }

            $this->info("Updating article {$article->title} ({$article->external_id})");
            $categorizer->updateCategoryForArticle($article);
            $this->info("Done.");
        }

        return self::SUCCESS;
    }
}
