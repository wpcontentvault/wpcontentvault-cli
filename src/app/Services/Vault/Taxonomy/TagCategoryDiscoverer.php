<?php

declare(strict_types=1);

namespace App\Services\Vault\Taxonomy;

use App\Context\Taxonomy\TagAttrs;
use App\Context\Taxonomy\TagMeta;
use App\Models\TagCategory;
use App\Repositories\LocaleRepository;
use App\Repositories\TagCategoryRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Database\Cleaner\TagCategoryCleaner;
use App\Services\Vault\Iterator\TagDirectoryIterator;
use App\Services\Vault\Meta\TagMetaManager;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Collection;

class TagCategoryDiscoverer
{
    public function __construct(
        private VaultPathResolver     $pathResolver,
        private VaultConfigLoader     $configLoader,
        private TagMetaManager        $metadataManager,
        private LocaleRepository      $locales,
        private TagCategoryRepository $tagCategories,
        private TagCategoryCleaner    $categoryCleaner,
        private TagDirectoryIterator  $tagIterator,
        private ApplicationOutput     $output,
    ) {}

    public
    function discoverTagCategories(): void
    {
        $categories = $this->configLoader->loadFromPath(
            $this->pathResolver->getRoot(), 'tags.json'
        );

        $locales = $this->locales->getAllLocales();

        $oldCategoryIds = $this->tagCategories->getAllTagCategories()->pluck('id')->toArray();
        $newCategoryIds = [];

        $oldTagDirs = $this->tagIterator->getTagDirectoryNames();
        $newTagDirs = [];

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

            $processedTags = $this->populateTagsForCategory($category, $locales, $categoryData['tags'] ?? []);

            $newTagDirs = array_merge($newTagDirs, $processedTags);
        }

        $removedCategories = array_diff($oldCategoryIds, $newCategoryIds);

        $removedTagDirs = array_diff($oldTagDirs, $newTagDirs);

        foreach ($removedTagDirs as $removedTagDir) {
            $this->output->warning("Tag directory $removedTagDir deprecated and can be removed.");
        }

        $this->categoryCleaner->markCategoriesAsStale($removedCategories);
    }

    private
    function populateTagsForCategory(TagCategory $category, Collection $locales, array $tags): array
    {
        $created = [];

        foreach ($tags as $tag) {
            $created[] = $tag['slug'];

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

        return $created;
    }
}
