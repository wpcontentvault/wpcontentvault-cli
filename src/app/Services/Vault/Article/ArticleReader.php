<?php

declare(strict_types=1);

namespace App\Services\Vault\Article;

use App\Context\Markdown\PostMeta;
use App\Models\Article;
use App\Registry\SitesRegistry;
use App\Services\Database\Resolver\ArticleResolver;
use App\Services\Database\Serializer\ArticleSerializer;
use App\Services\Vault\Discovery\ArticleContextDiscovery;
use App\Services\Vault\Discovery\ArticleLocalizationsDiscovery;
use App\Services\Vault\Manifest\V1\ManifestReader;
use App\Services\Vault\Manifest\V1\ManifestUpdater;
use App\Services\Vault\MarkdownLoader;
use RuntimeException;

class ArticleReader
{
    public function __construct(
        private MarkdownLoader $loader,
        private ArticleResolver $resolver,
        private ArticleSerializer $serializer,
        private ManifestReader $manifestLoader,
        private ManifestUpdater $manifestUpdater,
        private SitesRegistry $sitesConfiguration,
        private ArticleLocalizationsDiscovery $localizationsDiscovery,
        private ArticleContextDiscovery $contextDiscovery,
    ) {}

    public function loadArticleFromPath(string $path): Article
    {
        // Always take original
        $name = 'original';
        $mainLocaleCode = $this->sitesConfiguration->getMainSiteLocaleCode();

        $rendered = $this->loader->loadBlocksFromPath($path, $name);

        $originalMeta = $this->manifestLoader->loadManifestFromPath($path, $name);
        $mainMeta = $this->resolveMainSiteMeta($path, $originalMeta);

        if ($originalMeta->locale->code !== $mainLocaleCode) {
            $mainName = $mainLocaleCode;
        } else {
            $mainName = $name;
        }

        // create article on the main site if it does not exist
        $article = $this->resolver->resolveArticle($path, $mainMeta, $originalMeta);

        if ($mainMeta->externalId == null) {
            $this->manifestUpdater->updateExternalIdAndUrl(
                $path,
                $mainName,
                intval($article->external_id),
                $article->url
            );
        }

        $this->localizationsDiscovery->discoverAndCreateLocalizations($article);

        $this->contextDiscovery->discoverContext($article);

        $this->serializer->serializeArticle($rendered, $article);

        return $article;
    }

    private function resolveMainSiteMeta(string $path, PostMeta $meta): PostMeta
    {
        $mainLocaleCode = $this->sitesConfiguration->getMainSiteLocaleCode();

        if ($meta->locale->code === $mainLocaleCode) {
            $mainMeta = $meta;
        } else {
            if ($this->manifestLoader->manifestExists($path, $mainLocaleCode) === false) {
                throw new RuntimeException("Manifest for $mainLocaleCode does not exist!");
            }

            $mainMeta = $this->manifestLoader->loadManifestFromPath($path, $mainLocaleCode);
        }

        return $mainMeta;
    }
}
