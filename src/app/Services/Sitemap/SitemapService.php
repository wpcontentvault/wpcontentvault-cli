<?php

declare(strict_types=1);

namespace App\Services\Sitemap;

use App\Enum\Wordpress\ArticleStatusEnum;
use App\Models\Article;
use App\Models\Category;
use App\Registry\SitesRegistry;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Services\Sitemap\Context\AlternateLinks;
use App\Services\Vault\Manifest\V1\ManifestReader;
use App\Services\Vault\VaultPathResolver;
use Carbon\CarbonImmutable;

class SitemapService
{
    public function __construct(
        private ArticleRepository  $articles,
        private CategoryRepository $categories,
        private VaultPathResolver  $vaultPathResolver,
        private ManifestReader     $manifestReader,
        private SitesRegistry      $sitesRegistry,
    ) {}

    public function updateArticlesSitemap()
    {
        $articlesList = $this->articles->getAllArticles();
        $categoriesList = $this->categories->getAllCategories();

        $builder = new SitemapBuilder();

        foreach ($categoriesList as $category) {
            $this->addCategoryToSitemap($builder, $category);
        }

        foreach ($articlesList as $article) {
            $this->addArticleToSitemap($builder, $article);
        }

        $sitemapContent = $builder->build();

        $sitemapsDir = $this->vaultPathResolver->getRoot() . 'sitemaps/';

        if (false === is_dir($sitemapsDir)) {
            mkdir($sitemapsDir, 0755, true);
        }

        file_put_contents($sitemapsDir . 'sitemap.xml', $sitemapContent);
    }

    private function addArticleToSitemap(SitemapBuilder $builder, Article $article): void
    {
        $localeLinks = new AlternateLinks();
        foreach ($article->localizations as $localization) {
            if (false === $this->sitesRegistry->hasSiteConnectorForLocale($localization->locale)) {
                continue;
            }

            $localeLinks->addLink($localization->url, $localization->locale->code);
        }

        $manifest = $this->manifestReader->loadManifestFromPath($article->path, 'original');

        if ($manifest->status !== ArticleStatusEnum::PUBLISHED->value) {
            return;
        }

        if ($manifest->publishedAt === null) {
            throw new \RuntimeException("Article with path {$article->path} has no publishedAt date in manifest");
        }

        $modifiedAt = $manifest->modifiedAt ?? $manifest->publishedAt;

        if ($modifiedAt > now()->subYears(5)) {
            $priority = 1;
            $changeFrequency = 'monthly';
        } else {
            $changeFrequency = 'yearly';
            $priority = 0.8;
        }

        if (empty($article->url)) {
            throw new \RuntimeException("Article with path {$article->path} has no url");
        }


        $builder->addUrl(
            $article->url,
            $changeFrequency,
            $manifest->modifiedAt ?? now(),
            $priority,
            $localeLinks,
        );
    }

    private function addCategoryToSitemap(SitemapBuilder $builder, Category $category): void
    {
        $mainLocaleCode = $this->sitesRegistry->getMainSiteLocaleCode();
        $mainLocalization = null;

        $localeLinks = new AlternateLinks();
        foreach ($category->localizations as $localization) {
            /** @var \App\Models\CategoryLocalization $localization */
            if (false === $this->sitesRegistry->hasSiteConnectorForLocale($localization->locale)) {
                continue;
            }

            if ($localization->locale->code === $mainLocaleCode) {
                $mainLocalization = $localization;
            }

            $localeLinks->addLink($localization->url, $localization->locale->code);
        }

        $modifiedAt = $category->articles()
            ->whereNotNull('modified_at')
            ->orderBy('modified_at', 'DESC')
            ->first()
            ->modified_at;
        if (null !== $modifiedAt) {
            $modifiedAt = $modifiedAt->toImmutable();
        }

        $priority = 0.8;
        $changeFrequency = 'monthly';

        if (empty($mainLocalization->url)) {
            throw new \RuntimeException("Category with slug {$category->slug} has no url");
        }


        $builder->addUrl(
            $mainLocalization->url,
            $changeFrequency,
            $modifiedAt ?? now(),
            $priority,
            $localeLinks,
        );
    }
}
