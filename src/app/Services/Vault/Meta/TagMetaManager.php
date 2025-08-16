<?php

declare(strict_types=1);

namespace App\Services\Vault\Meta;

use App\Context\Taxonomy\TagAttrs;
use App\Context\Taxonomy\TagMeta;
use App\Models\Locale;
use App\Repositories\TagCategoryRepository;
use App\Services\Vault\VaultPathResolver;

class TagMetaManager extends MetadataManager
{
    public function __construct(
        private VaultPathResolver     $vaultPathResolver,
        private TagCategoryRepository $tagCategories,
    ) {}

    public function readTagAttrs(string $slug): ?TagAttrs
    {
        $filePath = $this->resolveFilePath($this->vaultPathResolver->getRoot(), 'tags/' . $slug, 'attrs.json');

        if (file_exists($filePath) === false) {
            return null;
        }

        $data = file_get_contents($filePath);
        $json = json_decode($data, true);

        return new TagAttrs(
            slug: $json['slug'],
            category: $this->tagCategories->findTagCategoryBySlug($json['category']),
            description: $json['description'],
        );
    }

    public function readTagMeta(string $slug, Locale $locale): ?TagMeta
    {
        $filePath = $this->resolveFilePath($this->vaultPathResolver->getRoot(), 'tags/' . $slug, $locale->code . '.json');

        if (file_exists($filePath) === false) {
            return null;
        }

        $data = file_get_contents($filePath);
        $json = json_decode($data, true);

        return new TagMeta(
            name: $json['name'],
            externalId: $json['external_id'],
        );
    }

    public function writeTagAttrs(TagAttrs $meta): void
    {
        $filePath = $this->resolveFilePath($this->vaultPathResolver->getRoot(), 'tags/' . $meta->slug, 'attrs.json');

        $data = [
            'slug' => $meta->slug,
            'category' => $meta->category->slug,
            'description' => $meta->description,
        ];

        file_put_contents(
            $filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function writeTagMeta(string $slug, TagMeta $meta, Locale $locale): void
    {
        $filePath = $this->resolveFilePath($this->vaultPathResolver->getRoot(), 'tags/' . $slug, $locale->code . '.json');

        $data = [
            'name' => $meta->name,
            'external_id' => $meta->externalId,
        ];

        file_put_contents(
            $filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    protected function getPrefix(): string
    {
        return '';
    }
}
