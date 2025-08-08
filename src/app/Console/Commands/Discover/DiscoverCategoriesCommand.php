<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Category;
use App\Models\CategoryLocalization;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
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
    protected $description = 'Refresh categories from categories.json';

    /**
     * Execute the console command.
     */
    public function handle(
        LocaleRepository  $locales,
        VaultPathResolver $pathResolver,
        VaultConfigLoader $loader,
    ): int
    {
        $localesList = $locales->getAllLocales()->keyBy('code');

        $categories = $loader->loadFromPath($pathResolver->getRoot(), 'categories.json');

        $localizationsTable = (new CategoryLocalization)->getTable();

        foreach ($categories as $categoryData) {
            $category = Category::query()->where('slug', $categoryData['slug'])->first();

            if (null !== $category) {
                $category->description = $categoryData['description'];
            } else {
                $category = new Category();
                $category->slug = $categoryData['slug'];
                $category->description = $categoryData['description'];
            }
            $category->save();

            foreach ($categoryData['localizations'] as $code => $localizationData) {
                $locale = $localesList->get($code);

                DB::table($localizationsTable)->updateOrInsert([
                    'category_id' => $category->getKey(),
                    'locale_id' => $locale->getKey(),
                ], [
                    'external_id' => $localizationData['external_id'],
                    'name' => $localizationData['name'],
                ]);
            }
        }

        return self::SUCCESS;
    }
}
