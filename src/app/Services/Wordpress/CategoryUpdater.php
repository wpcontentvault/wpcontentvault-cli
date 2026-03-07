<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Taxonomy\CategoryMeta;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Services\Console\ApplicationOutput;
use WPAjaxConnector\WPAjaxConnectorPHP\Enum\TaxonomyType;

class CategoryUpdater
{
    public function __construct(
        private SitesRegistry     $sites,
        private ApplicationOutput $applicationOutput,
    ) {}

    public function updateCategory(string $slug, CategoryMeta $meta, Locale $locale): void
    {
        if (false === $this->sites->hasSiteConnectorForLocale($locale)) {
            $this->applicationOutput->warning("Category {$slug} not updated for {$locale->name} locale!");

            return;
        }

        $connector = $this->sites->getSiteConnectorByLocale($locale);

        $connector->setTermName($meta->externalId, TaxonomyType::CATEGORY, $meta->name);
        if (null !== $meta->description) {
            $connector->setTermDescription($meta->externalId, TaxonomyType::CATEGORY, $meta->description);
        }
    }
}
