<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Configuration\GlobalConfiguration;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Meta\TagMetaManager;
use App\Services\Wordpress\TagCreator;
use App\Services\Wordpress\TagUpdater;

class TagExporter
{
    public function __construct(
        private TagMetaManager      $tagMetaManager,
        private LocaleRepository    $locales,
        private TagCreator          $tagCreator,
        private TagUpdater          $tagUpdater,
        private GlobalConfiguration $globalConfiguration,
        private SitesRegistry       $sitesConfig,
    ) {}

    public function exportTag(string $path): void
    {
        $attrs = $this->tagMetaManager->readTagAttrs($path);

        foreach ($this->locales->getAllLocales() as $locale) {
            $meta = $this->tagMetaManager->readTagMeta($path, $locale);

            if (null === $meta) {
                continue;
            }

            if (null === $meta->externalId || $this->globalConfiguration->shouldUpdateTagIds()) {
                $tagData = $this->tagCreator->createTag($attrs, $meta, $locale);

                if (null === $tagData) {
                    continue;
                }

                $this->tagMetaManager->updateExternalIdAndUrl(
                    $path, $locale, $tagData->id, $tagData->url
                );
            } else {
                $this->tagUpdater->updateTag($attrs, $meta, $locale);
            }
        }

        $mainSite = $this->sitesConfig->getMainSiteConnector();
        $mainLocale = $this->locales->findLocaleByCode($this->sitesConfig->getMainSiteLocaleCode());
        $mainMeta = $this->tagMetaManager->readTagMeta($path, $mainLocale);

        foreach ($this->locales->getAllLocales() as $locale) {
            if($locale->code === $mainLocale->code) {
                continue;
            }

            $meta = $this->tagMetaManager->readTagMeta($path, $locale);

            if (null === $meta || null === $meta->externalId) {
                continue;
            }

            $mainSite->setTermTranslation($mainMeta->externalId, $locale->code, $meta->externalId);
        }
    }
}
