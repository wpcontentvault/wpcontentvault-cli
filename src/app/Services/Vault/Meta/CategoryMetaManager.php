<?php

declare(strict_types=1);

namespace App\Services\Vault\Meta;

use App\Context\Taxonomy\CategoryMeta;
use App\Models\Locale;
use App\Services\Vault\VaultPathResolver;

class CategoryMetaManager extends MetadataManager
{
    public function __construct(
        private VaultPathResolver $vaultPathResolver,
    ) {}

    public function readCategoryMeta(string $slug, Locale $locale): ?CategoryMeta
    {
        $filePath = $this->resolveFilePath($this->vaultPathResolver->getRoot(), 'categories/' . $slug, $locale->code . '.json');
        $dirPath = dirname($filePath);

        if (file_exists($filePath) === false) {
            return null;
        }

        $data = file_get_contents($filePath);
        $json = json_decode($data, true);

        if ($json === null) {
            throw new \RuntimeException("Invalid JSON in file: {$filePath}");
        }

        if (file_exists($dirPath . "/{$locale->code}.txt")) {
            $description = file_get_contents($dirPath . "/{$locale->code}.txt");
        } else {
            $description = null;
        }


        return new CategoryMeta(
            name: $json['name'],
            url: $json['url'],
            externalId: $json['external_id'],
            slug: $json['slug'] ?? null,
            description: $description,
        );
    }

    public function writeCategoryMeta(string $slug, CategoryMeta $meta, Locale $locale): void
    {
        $filePath = $this->resolveFilePath($this->vaultPathResolver->getRoot(), 'categories/' . $slug, $locale->code . '.json');
        $dirPath = dirname($filePath);

        $data = [
            'name' => $meta->name,
            'external_id' => $meta->externalId,
            'url' => $meta->url,
        ];

        file_put_contents(
            $filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        if (null !== $meta->description) {
            file_put_contents($dirPath . "/{$locale->code}.txt", $meta->description);
        }
    }

    public function updateExternalIdAndUrl(string $slug, Locale $locale, int $externalId, string $url): void
    {
        $filePath = $this->resolveFilePath($this->vaultPathResolver->getRoot(), 'categories/' . $slug, $locale->code . '.json');

        $meta = $this->readCategoryMeta($slug, $locale);

        $data = [
            'name' => $meta->name,
            'url' => $url,
            'external_id' => $externalId,
        ];

        if (null !== $meta->slug) {
            $data['slug'] = $meta->slug;
        }

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
