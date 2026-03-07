<?php

declare(strict_types=1);

namespace App\Services\Vault\Meta;

use App\Context\Taxonomy\TagAttrs;
use App\Context\Taxonomy\TagMeta;
use App\Models\Locale;

class TagMetaManager extends MetadataManager
{
    public function __construct() {}

    public function readTagAttrs(string $path): ?TagAttrs
    {
        $filePath = $this->resolveFilePath($path, '', 'attrs.json');

        if (file_exists($filePath) === false) {
            return null;
        }

        $data = file_get_contents($filePath);
        $json = json_decode($data, true);

        return new TagAttrs(
            slug: $json['slug'],
        );
    }

    public function readTagMeta(string $path, Locale $locale): ?TagMeta
    {
        $filePath = $this->resolveFilePath($path, '', $locale->code . '.json');
        $dirPath = dirname($filePath);

        if (file_exists($filePath) === false) {
            return null;
        }

        $data = file_get_contents($filePath);
        $json = json_decode($data, true);

        if (file_exists($dirPath . "/{$locale->code}.txt")) {
            $description = file_get_contents($dirPath . "/{$locale->code}.txt");
        } else {
            $description = null;
        }

        return new TagMeta(
            name: $json['name'],
            url: $json['url'] ?? null,
            externalId: $json['external_id'],
            slug: $json['slug'] ?? null,
            description: $description,
        );
    }

    public function writeTagAttrs(string $path, TagAttrs $meta): void
    {
        $filePath = $this->resolveFilePath($path, '', 'attrs.json');

        $data = [
            'slug' => $meta->slug,
        ];

        file_put_contents(
            $filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function writeTagMeta(string $path, TagMeta $meta, Locale $locale): void
    {
        $filePath = $this->resolveFilePath($path, '', $locale->code . '.json');
        $dirPath = dirname($filePath);

        $data = [
            'name' => $meta->name,
            'url' => $meta->url,
            'external_id' => $meta->externalId,
        ];

        if(null !== $meta->slug) {
            $data['slug'] = $meta->slug;
        }

        file_put_contents(
            $filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        if (null !== $meta->description) {
            file_put_contents($dirPath . "/{$locale->code}.txt", $meta->description);
        }
    }

    public function updateExternalIdAndUrl(string $path, Locale $locale, int $externalId, string $url): void
    {
        $filePath = $this->resolveFilePath($path, '', $locale->code . '.json');

        $meta = $this->readTagMeta($path, $locale);

        $data = [
            'name' => $meta->name,
            'url' => $url,
            'external_id' => $externalId,
        ];

        if(null !== $meta->slug) {
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
