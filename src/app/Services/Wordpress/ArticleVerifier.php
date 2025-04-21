<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Markdown\PostMeta;
use App\Registry\SitesRegistry;
use RuntimeException;

class ArticleVerifier
{
    public function __construct(
        private SitesRegistry $sitesConfig,
    ) {}

    public function verifyPostMetaOnMainSite(PostMeta $mainMeta): bool
    {
        $mainSite = $this->sitesConfig->getMainSiteConnector();
        $mainLocaleCode = $this->sitesConfig->getMainSiteLocaleCode();

        if ($mainMeta->locale->code !== $mainLocaleCode) {
            throw new RuntimeException('Expected meta for main site!');
        }

        $remoteArticle = $mainSite->getPost($mainMeta->externalId);

        if ($remoteArticle->url !== $mainMeta->url) {
            throw new RuntimeException("Url missmatch, local url is {$mainMeta->url} but remote is {$remoteArticle->url}");
        }

        return true;
    }
}
