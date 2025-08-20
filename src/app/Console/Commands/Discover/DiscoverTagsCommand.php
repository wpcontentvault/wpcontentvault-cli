<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Console\Commands\AbstractApplicationCommand;
use App\Services\Vault\Taxonomy\TagCategoryDiscoverer;
use App\Services\Vault\Taxonomy\TagDiscoverer;

class DiscoverTagsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discover-tags {--update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh tags from tags directory';

    /**
     * Execute the console command.
     */
    public function handle(
        TagCategoryDiscoverer $tagCategoryDiscoverer,
        TagDiscoverer $tagDiscoverer
    ): int
    {
        $tagCategoryDiscoverer->discoverTagCategories();
        $tagDiscoverer->discoverAllTags();

        return self::SUCCESS;
    }
}
