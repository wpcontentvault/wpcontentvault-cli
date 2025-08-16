<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Tag;
use App\Models\TagLocalization;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Taxonomy\TagCategoryDiscoverer;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Facades\DB;

class DiscoverTagCategoriesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discover-tag-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh tags from tags.json';

    /**
     * Execute the console command.
     */
    public function handle(
        TagCategoryDiscoverer $tagCategoryDiscoverer,
    ): int
    {
        $tagCategoryDiscoverer->discoverTagCategories();

        return self::SUCCESS;
    }
}
