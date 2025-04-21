<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Category;
use App\Models\CategoryLocalization;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use Illuminate\Support\Facades\DB;

class DiscoverCategoriesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh categories from categories.json';

    /**
     * Execute the console command.
     */
    public function handle(LocaleRepository $locales, SitesRegistry $sitesConfig): int
    {
        $localesList = $locales->getAllLocales()->keyBy('code');

        $categoriesData = file_get_contents(resource_path('articles/Articles/categories.json'));
        $categories = json_decode($categoriesData, true);

        $categoriesTable = (new Category)->getTable();
        $localizationsTable = (new CategoryLocalization)->getTable();

        foreach ($categories as $categoryData) {
            DB::table($categoriesTable)->updateOrInsert(['slug' => $categoryData['slug']], []);

            $category = Category::query()->where('slug', $categoryData['slug'])->first();

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
