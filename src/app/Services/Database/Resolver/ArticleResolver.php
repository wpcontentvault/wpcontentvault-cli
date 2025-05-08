<?php

declare(strict_types=1);

namespace App\Services\Database\Resolver;

use App\Context\Markdown\PostMeta;
use App\Models\Article;
use App\Registry\SitesRegistry;
use App\Repositories\ArticleRepository;
use App\Services\Wordpress\ArticleVerifier;
use App\Services\Wordpress\PostCreator;
use RuntimeException;

class ArticleResolver
{
    public function __construct(
        private ArticleRepository $articles,
        private SitesRegistry     $sitesConfiguration,
        private PostCreator       $articleCreator,
        private ArticleVerifier   $articleVerifier,
    ) {}

    public function resolveArticle(string $path, PostMeta $mainMeta, PostMeta $originalMeta): Article
    {
        $article = $this->articles->findArticleByPath($path);

        // If external id in the file was reset, reset it also in DB
        if ($article !== null && $mainMeta->externalId !== null) {
            $article->title = $mainMeta->title;
            $article->author = $mainMeta->author;
            $article->published_at = $mainMeta->publishedAt;
            $article->modified_at = $mainMeta->modifiedAt;
            $article->url = $mainMeta->url;
            $article->save();

            return $article;
        }

        if ($article === null) {
            $article = $this->articles->createModel();
        }

        $mainLocaleCode = $this->sitesConfiguration->getMainSiteLocaleCode();

        if ($mainMeta->locale->code !== $mainLocaleCode) {
            throw new RuntimeException('Expected meta for main site!');
        }

        if ($mainMeta->externalId === null) {
            $postInfo = $this->articleCreator->createPostOnMainSite($mainMeta);

            $externalId = $postInfo->id;
            $url = $postInfo->url;
        } else {
            $externalId = $mainMeta->externalId;
            $url = $mainMeta->url;

            $this->articleVerifier->verifyPostMetaOnMainSite($mainMeta);
        }

        $article->locale()->associate($originalMeta->locale);
        $article->path = $path;
        $article->title = $mainMeta->title;
        $article->author = $mainMeta->author;
        $article->published_at = $mainMeta->publishedAt;
        $article->modified_at = $mainMeta->modifiedAt;
        $article->external_id = $externalId;
        $article->url = $url;
        $article->save();

        return $article;
    }
}
