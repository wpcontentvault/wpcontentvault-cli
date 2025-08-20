<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Taxonomy\TagAttrs;
use App\Context\Taxonomy\TagMeta;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Services\Console\ApplicationOutput;
use WPAjaxConnector\WPAjaxConnectorPHP\Enum\TaxonomyType;

class TagUpdater
{
    public function __construct(
        private SitesRegistry     $sites,
        private ApplicationOutput $applicationOutput,
    ) {}

    public function updateTag(TagAttrs $attrs, TagMeta $meta, Locale $locale): void
    {
        if (false === $this->sites->hasSiteConnectorForLocale($locale)) {
            $this->applicationOutput->warning("Tag {$attrs->slug} not updated for {$locale->name} locale!");

            return;
        }

        $connector = $this->sites->getSiteConnectorByLocale($locale);

        $connector->setTermName($meta->externalId, TaxonomyType::TAG, $meta->name);
        $connector->setTermSlug($meta->externalId, TaxonomyType::TAG, $attrs->slug);
    }
}
