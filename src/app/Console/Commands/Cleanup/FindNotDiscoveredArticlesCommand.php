<?php

declare(strict_types=1);

namespace App\Console\Commands\Cleanup;

use App\Console\Commands\AbstractApplicationCommand;
use App\Registry\SitesRegistry;
use App\Repositories\ArticleRepository;
use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Finder\Finder;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\PostData;

class FindNotDiscoveredArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find-not-discovered-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all articles that exist on remote website but dont exist in database';

    /**
     * Execute the console command.
     */

    public function handle(SitesRegistry $sites, ArticleRepository $articles): int
    {
        $mainSite = $sites->getMainSiteConnector();

        $perPage = 10;
        $page = 1;
        $hasNext = true;

        while ($hasNext) {
            $posts = $mainSite->query('')
                ->onlyPublished(true)
                ->orderBy('modified', 'desc')
                ->page($page)
                ->count($perPage)
                ->getPosts();

            $this->processPosts($posts->posts, $articles);

            $hasNext = $posts->hasMore;

            if ($hasNext) {
                $page++;
            }
        }

        return self::SUCCESS;
    }

    private function processPosts(array $posts, ArticleRepository $articles): void
    {
        foreach ($posts as $post) {
            /** @var PostData $post */

            if (null === $articles->findArticleByExternalId($post->id)) {
                $this->info($post->url);
            }
        }
    }
}
