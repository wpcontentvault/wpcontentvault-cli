<?php

declare(strict_types=1);

namespace App\Services\Vault\Taxonomy;

use App\Models\Category;
use App\Models\CategoryLocalization;
use App\Models\Tag;
use App\Repositories\CategoryRepository;
use App\Repositories\LocaleRepository;
use App\Services\Database\Cleaner\TagCleaner;
use App\Services\Vault\Iterator\CategoryDirectoryIterator;
use App\Services\Vault\Meta\CategoryMetaManager;
use Illuminate\Support\Collection;

class CategoryDiscoverer
{
    private Collection $localesList;

    public function __construct(
        private CategoryDirectoryIterator $categoryIterator,
        private CategoryMetaManager       $metadataManager,
        private LocaleRepository     $locales,
        private CategoryRepository        $categories,
        private TagCleaner           $tagCleaner,
    )
    {
        $this->localesList = $this->locales->getAllLocales();
    }

    public function discoverAllCategories(): void
    {
        $oldCategoryIds = $this->categories->getAllCategories()->pluck('id')->toArray();
        $newCategoryIds = [];

        foreach ($this->categoryIterator->getCategoryDirectories() as $dir) {
            /** @var \SplFileInfo $dir */
            $slug = $dir->getBasename();

            $newCategoryIds[] = $this->discoverCategoryFromPath($slug);
        }

        $removedTags = array_diff($oldCategoryIds, $newCategoryIds);

        $this->tagCleaner->markTagsAsStale($removedTags);
    }

    public function discoverCategoryFromPath(string $slug): string
    {
        $existing = $this->categories->findCategoryBySlug($slug);

        if (null === $existing) {
            $existing = new Category();
            $existing->slug = $slug;
        }

        $existing->save();

        foreach ($this->localesList as $locale) {
            $meta = $this->metadataManager->readCategoryMeta($slug, $locale);

            if (null === $meta) {
                continue;
            }

            $localization = $existing->findLocalizationByLocale($locale);

            if (null === $localization) {
                $localization = new CategoryLocalization();
                $localization->category()->associate($existing);
                $localization->locale()->associate($locale);
            }

            $localization->name = $meta->name;
            $localization->url = $meta->url;
            $localization->external_id = $meta->externalId;
            $localization->save();
        }

        return $existing->getKey();
    }
}
