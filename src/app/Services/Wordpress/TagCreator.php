<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Taxonomy\TagAttrs;
use App\Context\Taxonomy\TagMeta;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Services\Console\ApplicationOutput;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\TagData;

class TagCreator
{
    public function __construct(
        private SitesRegistry     $sites,
        private ApplicationOutput $applicationOutput,
    ) {}

    public function createTag(TagAttrs $attrs, TagMeta $meta, Locale $locale): ?TagData
    {
        if (null !== $meta->externalId) {
            $this->applicationOutput->warning("Tag {$attrs->slug} external_id is not null, it will be overridden!");
        }

        if (false === $this->sites->hasSiteConnectorForLocale($locale)) {
            $this->applicationOutput->warning("Tag {$attrs->slug} not created for {$locale->name} locale!");

            return null;
        }

        $connector = $this->sites->getSiteConnectorByLocale($locale);

        $slug = $meta->slug ?? $attrs->slug;

        return $connector->addTag($meta->name, $slug);
    }
}
