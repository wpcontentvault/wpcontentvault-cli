<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Registry\SitesRegistry;
use Illuminate\Console\Command;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\PostData;

class GetSiteUrlsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-site-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all post urls available';

    /**
     * Execute the console command.
     */
    public function handle(SitesRegistry $sites): int
    {
        $mainSite = $sites->getMainSiteConnector();

        $perPage = 10;
        $page = 1;
        $hasNext = true;

        while ($hasNext) {
            $posts = $mainSite->query('wp-block-semmi-file-content')
                ->onlyPublished(true)
                ->orderBy('modified', 'desc')
                ->page($page)
                ->count($perPage)
                ->getPosts();

            $this->processPosts($posts->posts);

            $hasNext = $posts->hasMore;

            if ($hasNext) {
                $page++;
            }
        }

        return self::SUCCESS;
    }

    private function processPosts(array $posts): void
    {
        foreach ($posts as $post) {
            /** @var PostData $post */
            echo $post->id."\n";
        }
    }
}
