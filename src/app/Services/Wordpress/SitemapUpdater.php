<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Registry\SitesRegistry;
use App\Services\Vault\VaultPathResolver;

class SitemapUpdater
{
    public function __construct(
        private SitesRegistry     $sitesRegistry,
        private VaultPathResolver $vaultPathResolver
    ) {}

    public function updateSitemap(): void
    {
        $sitemapFile = $this->vaultPathResolver->getRoot() . 'sitemaps/sitemap.xml';

        if (false === file_exists($sitemapFile)) {
            throw new \RuntimeException("Sitemap file not found at path: " . $sitemapFile);
        }

        $sitemapData = file_get_contents($sitemapFile);

        $this->sitesRegistry->getMainSiteConnector()->updateSitemap($sitemapData);
    }
}
