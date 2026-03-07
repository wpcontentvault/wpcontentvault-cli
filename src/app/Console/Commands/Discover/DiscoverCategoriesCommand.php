<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Category;
use App\Models\CategoryLocalization;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Taxonomy\CategoryDiscoverer;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Facades\DB;

class DiscoverCategoriesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discover-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh categories from Vault';

    /**
     * Execute the console command.
     */
    public function handle(
        CategoryDiscoverer $discoverer,
    ): int
    {
        $discoverer->discoverAllCategories();

        return self::SUCCESS;
    }
}
