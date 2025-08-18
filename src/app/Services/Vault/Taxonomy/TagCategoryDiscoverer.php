<?php

declare(strict_types=1);

namespace App\Services\Vault\Taxonomy;

use App\Context\Taxonomy\TagAttrs;
use App\Context\Taxonomy\TagMeta;
use App\Models\TagCategory;
use App\Repositories\LocaleRepository;
use App\Repositories\TagCategoryRepository;
use App\Services\Database\Cleaner\TagCategoryCleaner;
use App\Services\Vault\Meta\TagMetaManager;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Collection;

class TagCategoryDiscoverer
{
    public function __construct(
        private VaultPathResolver $pathResolver,
        private VaultConfigLoader $configLoader,
        private TagMetaManager    $metadataManager,
        private LocaleRepository  $locales,
        private TagCategoryRepository $tagCategories,
        private TagCategoryCleaner $categoryCleaner,
    ) {}

    public function discoverTagCategories(): void
    {
        $categories = $this->configLoader->loadFromPath(
            $this->pathResolver->getRoot(), 'tags.json'
        );

        $locales = $this->locales->getAllLocales();

        $oldCategoryIds = $this->tagCategories->getAllTagCategories()->pluck('id')->toArray();
        $newCategoryIds = [];

        foreach ($categories as $categoryData) {
            $category = TagCategory::query()->where('slug', $categoryData['slug'])->first();

            if (null !== $category) {
                $category->is_hidden = $categoryData['is_hidden'] ?? false;
            } else {
                $category = new TagCategory();
                $category->slug = $categoryData['slug'];
                $category->is_hidden = $categoryData['is_hidden'] ?? false;
            }

            $newCategoryIds[] = $category->getKey();

            $category->save();

            $this->populateTagsForCategory($category, $locales, $categoryData['tags'] ?? []);
        }

        $removedCategories = array_diff($oldCategoryIds, $newCategoryIds);

        $this->categoryCleaner->markCategoriesAsStale($removedCategories);
    }

    private function populateTagsForCategory(TagCategory $category, Collection $locales, array $tags): void
    {
        foreach ($tags as $tag) {
            $this->metadataManager->writeTagAttrs(
                new TagAttrs(
                    slug: $tag['slug'],
                    category: $category,
                    description: $tag['description']
                )
            );

            foreach ($locales as $locale) {
                $existing = $this->metadataManager->readTagMeta($tag['slug'], $locale);
                $name = ($existing?->name) ?? ucfirst($tag['slug']);
                $externalId = $existing?->externalId;

                $meta = new TagMeta(
                    name: $name,
                    externalId: $externalId,
                );

                $this->metadataManager->writeTagMeta($tag['slug'], $meta, $locale);
            }
        }
    }
}
