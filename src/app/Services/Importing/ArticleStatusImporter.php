<?php

declare(strict_types=1);

namespace App\Services\Importing;

use App\Models\Article;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V2\ManifestReader;
use App\Services\Vault\Manifest\V2\ManifestUpdater;

class ArticleStatusImporter
{
    public function __construct(
        private SitesRegistry        $sitesConfig,
        private ManifestReader       $manifestReader,
        private ManifestNameResolver $manifestNameResolver,
        private ManifestUpdater      $manifestUpdater,
    ) {}

    public function pullArticleStatus(Article $article, Locale $locale): void
    {
        $name = $this->manifestNameResolver->resolveName($article, $locale);

        $meta = $this->manifestReader->loadManifestFromPath($article->path, $name);

        if (false === $this->sitesConfig->hasSiteConnectorForLocale($meta->locale)) {
            return;
        }

        $connector = $this->sitesConfig->getSiteConnectorByLocale($meta->locale);

        $localization = $article->findLocalizationByLocale($locale);

        $postData = $connector->getPost($localization->external_id);

        $this->manifestUpdater->updatePublishedAndModifiedDates(
            $article->path,
            $name,
            $postData->publishedAt,
            $postData->modifiedAt,
        );
    }
}
