<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Taxonomy\CategoryMeta;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Services\Console\ApplicationOutput;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\TermData;

class CategoryCreator
{
    public function __construct(
        private SitesRegistry     $sites,
        private ApplicationOutput $applicationOutput,
    ) {}

    public function createCategory(string $slug, CategoryMeta $meta, Locale $locale): ?TermData
    {
        if (null !== $meta->externalId) {
            $this->applicationOutput->warning("Category {$slug} external_id is not null, it will be overridden!");
        }

        if (false === $this->sites->hasSiteConnectorForLocale($locale)) {
            $this->applicationOutput->warning("Category {$slug} not created for {$locale->name} locale!");

            return null;
        }

        $connector = $this->sites->getSiteConnectorByLocale($locale);

        $slug = $meta->slug ?? $slug;

        return $connector->addCategory($meta->name, $slug);
    }
}
