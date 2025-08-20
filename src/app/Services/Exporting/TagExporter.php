<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Configuration\GlobalConfiguration;
use App\Context\Taxonomy\TagMeta;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Meta\TagMetaManager;
use App\Services\Wordpress\TagCreator;
use App\Services\Wordpress\TagUpdater;

class TagExporter
{
    public function __construct(
        private TagMetaManager   $tagMetaManager,
        private LocaleRepository $locales,
        private TagCreator       $tagCreator,
        private TagUpdater       $tagUpdater,
        private GlobalConfiguration $globalConfiguration,
    ) {}

    public function exportTag(string $slug): void
    {
        $attrs = $this->tagMetaManager->readTagAttrs($slug);

        foreach ($this->locales->getAllLocales() as $locale) {
            $meta = $this->tagMetaManager->readTagMeta($slug, $locale);

            if (null === $meta) {
                continue;
            }

            if (null === $meta->externalId || $this->globalConfiguration->shouldUpdateTagIds()) {
                $tagId = $this->tagCreator->createTag($attrs, $meta, $locale);

                if (null === $tagId) {
                    continue;
                }

                $newMeta = new TagMeta(
                    name: $meta->name,
                    externalId: $tagId
                );

                $this->tagMetaManager->writeTagMeta($slug, $newMeta, $locale);
            } else {
                $this->tagUpdater->updateTag($attrs, $meta, $locale);
            }
        }
    }
}
