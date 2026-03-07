<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Configuration\GlobalConfiguration;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Meta\CategoryMetaManager;
use App\Services\Vault\Meta\TagMetaManager;
use App\Services\Wordpress\CategoryCreator;
use App\Services\Wordpress\CategoryUpdater;
use App\Services\Wordpress\TagCreator;
use App\Services\Wordpress\TagUpdater;
use WPAjaxConnector\WPAjaxConnectorPHP\Enum\TaxonomyType;

class CategoryExporter
{
    public function __construct(
        private CategoryMetaManager $categoryMetaManager,
        private LocaleRepository    $locales,
        private CategoryCreator     $categoryCreator,
        private CategoryUpdater     $categoryUpdater,
        private GlobalConfiguration $globalConfiguration,
        private SitesRegistry       $sitesConfig,
    ) {}

    public function exportCategory(string $slug): void
    {
        foreach ($this->locales->getAllLocales() as $locale) {
            $meta = $this->categoryMetaManager->readCategoryMeta($slug, $locale);

            if (null === $meta) {
                continue;
            }

            if (null === $meta->externalId || $this->globalConfiguration->shouldUpdateTagIds()) {
                $categoryData = $this->categoryCreator->createCategory($slug, $meta, $locale);

                if (null === $categoryData) {
                    continue;
                }

                $this->categoryMetaManager->updateExternalIdAndUrl(
                    $slug, $locale, $categoryData->id, $categoryData->url
                );
            }else {
                $this->categoryUpdater->updateCategory($slug, $meta, $locale);
            }
        }

        $mainSite = $this->sitesConfig->getMainSiteConnector();
        $mainLocale = $this->locales->findLocaleByCode($this->sitesConfig->getMainSiteLocaleCode());
        $mainMeta = $this->categoryMetaManager->readCategoryMeta($slug, $mainLocale);

        foreach ($this->locales->getAllLocales() as $locale) {
            if ($locale->code === $mainLocale->code) {
                continue;
            }

            $meta = $this->categoryMetaManager->readCategoryMeta($slug, $locale);

            if (null === $meta || null === $meta->externalId) {
                continue;
            }

            $mainSite->setTermTranslation(
                $mainMeta->externalId,
                $locale->code,
                $meta->externalId,
                TaxonomyType::CATEGORY
            );
        }
    }
}
