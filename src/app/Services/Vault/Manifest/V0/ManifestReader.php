<?php

declare(strict_types=1);

namespace App\Services\Vault\Manifest\V1;

use App\Context\Markdown\PostMeta;
use App\Repositories\LocaleRepository;
use App\Services\Database\Resolver\CategoryResolver;
use App\Services\Database\Resolver\TagResolver;
use App\Services\Vault\Meta\ArticleIdMeta;
use Carbon\CarbonImmutable;

class ManifestReader
{
    public function __construct(
        private readonly LocaleRepository $locales,
        private readonly CategoryResolver $categoryResolver,
        private readonly TagResolver      $tagResolver,
        private readonly ArticleIdMeta    $articleIdMeta,
    ) {}

    public function manifestExists(string $path, string $name): bool
    {
        return file_exists($path . '/' . $name . '.json');
    }

    public function loadManifestFromPath(string $path, string $name)
    {
        $data = file_get_contents($path . '/' . $name . '.json');
        $json = json_decode($data, true);

        assert(intval($json['version'] ?? null) == 1);

        $locale = $this->locales->findLocaleByCode($json['locale']);
        assert($locale !== null);

        assert(empty($json['title']) === false);

        if (empty($json['published_at']) === false) {
            $createdAt = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $json['published_at']);
        } else {
            $createdAt = null;
        }

        if (empty($json['modified_at']) === false) {
            $modifiedAt = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $json['modified_at']);
        } else {
            $modifiedAt = null;
        }

        $serializedId = $this->articleIdMeta->readSerializedId($path);

        $category = $this->categoryResolver->resolveCategoryByName($json['category'] ?? null, $locale);

        $tags = [];

        foreach ($json['tags'] ?? [] as $tag) {
            $resolvedTag = $this->tagResolver->resolveTagByName($tag, $locale);
            if (null !== $resolvedTag) {
                $tags[] = $resolvedTag;
            }
        }

        return new PostMeta(
            locale: $locale,
            title: $json['title'],
            status: $json['status'] ?? 'draft',
            author: $json['author'] ?? '',
            publishedAt: $createdAt,
            modifiedAt: $modifiedAt,
            url: $json['url'] ?? null,
            externalId: empty($json['external_id']) === false ? $json['external_id'] : null,
            category: $category,
            tags: $tags,
            serializedId: $serializedId,
        );
    }
}
