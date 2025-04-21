<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Markdown\PostMeta;
use App\Registry\SitesRegistry;
use RuntimeException;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\PostData;

class PostCreator
{
    public function __construct(
        private SitesRegistry $sitesConfig,
    ) {}

    public function createPostOnMainSite(PostMeta $meta): PostData
    {
        $mainLocaleCode = $this->sitesConfig->getMainSiteLocaleCode();
        $mainSite = $this->sitesConfig->getMainSiteConnector();

        if ($mainLocaleCode !== $meta->locale->code) {
            throw new RuntimeException('Expected meta for main site!');
        }

        if ($meta->externalId !== null) {
            throw new RuntimeException('Article already exists!');
        }

        return $mainSite->addPost($meta->title, '');
    }

    public function createPostOnSubSite(PostMeta $meta): PostData
    {
        $mainLocaleCode = $this->sitesConfig->getMainSiteLocaleCode();
        $site = $this->sitesConfig->getSiteConnectorByLocale($meta->locale);

        if ($mainLocaleCode == $meta->locale->code) {
            throw new RuntimeException('Expected meta for not main site!');
        }

        if ($meta->externalId !== null) {
            throw new RuntimeException('Article already exists!');
        }

        return $site->addPost($meta->title, '');
    }
}
