<?php

declare(strict_types=1);

namespace App\Services\Vault\Manifest\V2;

use App\Enum\Wordpress\ArticleStatusEnum;
use App\Models\Category;
use DateTimeInterface;
use RuntimeException;

class ManifestUpdater
{
    public function updateExternalIdAndUrl(string $path, string $name, int $externalId, string $url): void
    {
        $json = $this->deserialize($path, $name);

        if (empty($json['external_id']) === false) {
            throw new RuntimeException("Can't update external_id");
        }

        $json['external_id'] = $externalId;
        $json['url'] = $url;

        $this->serialize($path, $name, $json);
    }

    public function updatePublishedAndModifiedDates(string $path, string $name, DateTimeInterface $publishedAt, DateTimeInterface $modifiedAt): void
    {
        $json = $this->deserialize($path, $name);

        $json['published_at'] = $publishedAt->format('Y-m-d H:i:s');
        $json['modified_at'] = $modifiedAt->format('Y-m-d H:i:s');

        $this->serialize($path, $name, $json);
    }

    public function updateStatus(string $path, string $name, ArticleStatusEnum $status): void
    {
        $json = $this->deserialize($path, $name);

        $json['status'] = $status->value;

        $this->serialize($path, $name, $json);
    }

    public function updateCategory(string $path, string $name, Category $category): void
    {
        $json = $this->deserializeShared($path);

        $json['category'] = $category->slug;

        $this->serializeShared($path, $json);
    }

    public function updateTags(string $path, string $name, array $tags): void
    {
        $json = $this->deserializeShared($path);

        $json['tags'] = collect($tags)->pluck('slug')->toArray();

        $this->serializeShared($path, $json);

    }

    private function deserialize(string $path, string $name): array
    {
        $fileName = $path . '/' . $name . '.json';
        $data = file_get_contents($fileName);
        $json = json_decode($data, true);

        return $json;
    }

    private function serialize(string $path, string $name, array $json): void
    {
        $fileName = $path . '/' . $name . '.json';

        file_put_contents(
            $fileName,
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function deserializeShared(string $path): array
    {
        $fileName = $path . '/attrs.json';
        $data = file_get_contents($fileName);
        $json = json_decode($data, true);

        return $json;
    }

    private function serializeShared(string $path, array $json): void
    {
        $fileName = $path . '/attrs.json';

        file_put_contents(
            $fileName,
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
