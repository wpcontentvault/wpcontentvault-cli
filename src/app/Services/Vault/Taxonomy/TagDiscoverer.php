<?php

declare(strict_types=1);

namespace App\Services\Vault\Taxonomy;

use App\Models\Tag;
use App\Models\TagLocalization;
use App\Repositories\LocaleRepository;
use App\Repositories\TagRepository;
use App\Services\Database\Cleaner\TagCleaner;
use App\Services\Vault\Meta\TagMetaManager;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

class TagDiscoverer
{
    private Collection $localesList;

    public function __construct(
        private VaultPathResolver $pathResolver,
        private TagMetaManager    $metadataManager,
        private LocaleRepository  $locales,
        private TagRepository     $tags,
        private TagCleaner        $tagCleaner,
    )
    {
        $this->localesList = $this->locales->getAllLocales();
    }

    public function discoverAllTags(): void
    {
        $finder = new Finder();
        $finder->name('*')
            ->notName('.')
            ->notName('..');
        $finder->sortByName();

        $tagsPath = $this->pathResolver->getRoot() . 'tags/';

        $oldTagIds = $this->tags->getAllTags()->pluck('id')->toArray();
        $newTagIds = [];

        foreach ($finder->directories()->in($tagsPath) as $dir) {
            /** @var \SplFileInfo $dir */
            $newTagIds[] = $this->discoverTagFromPath($dir->getBasename());
        }

        $removedTags = array_diff($oldTagIds, $newTagIds);

        $this->tagCleaner->markTagsAsStale($removedTags);
    }

    public function discoverTagFromPath(string $path): string
    {
        $attrs = $this->metadataManager->readTagAttrs($path);

        if (null === $attrs) {
            throw new \RuntimeException("Can't read tag attributes from $path");
        }

        $existing = $this->tags->findTagBySlug($attrs->slug);

        if (null === $existing) {
            $existing = new Tag();
            $existing->slug = $attrs->slug;
        }

        $existing->category()->associate($attrs->category);
        $existing->description = $attrs->description;
        $existing->save();

        foreach ($this->localesList as $locale) {
            $meta = $this->metadataManager->readTagMeta($path, $locale);

            if (null === $meta) {
                continue;
            }

            $localization = $existing->findLocalizationByLocale($locale);

            if (null === $localization) {
                $localization = new TagLocalization();
                $localization->tag()->associate($existing);
                $localization->locale()->associate($locale);
            }

            $localization->name = $meta->name;
            $localization->external_id = $meta->externalId;
            $localization->save();
        }

        return $existing->getKey();
    }
}
