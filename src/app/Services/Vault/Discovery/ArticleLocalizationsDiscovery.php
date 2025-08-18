<?php

declare(strict_types=1);

namespace App\Services\Vault\Discovery;

use App\Context\Markdown\PostMeta;
use App\Models\Article;
use App\Models\ArticleLocalization;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V2\ManifestReader;
use App\Services\Vault\Manifest\V2\ManifestUpdater;
use App\Services\Wordpress\PostCreator;

class ArticleLocalizationsDiscovery
{
    public function __construct(
        private SitesRegistry $sitesConfiguration,
        private LocaleRepository $locales,
        private ManifestReader $manifestReader,
        private ManifestNameResolver $manifestNameResolver,
        private PostCreator $postCreator,
        private ManifestUpdater $manifestUpdater,
    ) {}

    public function discoverAndCreateLocalizations(Article $article): void
    {
        $mainLocaleCode = $this->sitesConfiguration->getMainSiteLocaleCode();
        $list = $this->locales->getAllLocales();

        foreach ($list as $locale) {
            $name = $this->manifestNameResolver->resolveName($article, $locale);

            // Skip if there is no such locale for the article
            $exists = $this->manifestReader->manifestExists($article->path, $name);
            if ($locale->code !== $mainLocaleCode && $exists === false) {
                continue;
            }

            $meta = $this->manifestReader->loadManifestFromPath($article->path, $name);

            if ($locale->code !== $mainLocaleCode && $meta->externalId == null) {
                // Create post on localization site if it does not exist
                $this->createRemotePostAndUpdateManifest($meta, $article->path, $name);

                // Re-read meta
                $meta = $this->manifestReader->loadManifestFromPath($article->path, $name);
            }

            $this->updateArticleLocalization($article, $locale, $meta);
        }
    }

    private function createRemotePostAndUpdateManifest(PostMeta $meta, string $path, string $name): void
    {
        if ($meta->externalId === null) {
            $postInfo = $this->postCreator->createPostOnSubSite($meta);

            $this->manifestUpdater->updateExternalIdAndUrl(
                $path,
                $name,
                $postInfo->id,
                $postInfo->url
            );
        }
    }

    private function updateArticleLocalization(Article $article, Locale $locale, PostMeta $meta): void
    {
        $localization = $article->findLocalizationByLocale($locale);

        if ($localization === null) {
            $localization = new ArticleLocalization;
            $localization->locale()->associate($locale);
            $localization->article()->associate($article);
        }

        $localization->external_id = $meta->externalId;
        $localization->url = $meta->url;
        $localization->title = $meta->title;
        $localization->is_original = $meta->locale->code === $article->locale->code;
        $localization->save();
    }
}
