<?php

declare(strict_types=1);

namespace App\Services\Vault\Manifest\V1;

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

    private function deserialize(string $path, string $name): array
    {
        $fileName = $path.'/'.$name.'.json';
        $data = file_get_contents($fileName);
        $json = json_decode($data, true);

        return $json;
    }

    private function serialize(string $path, string $name, array $json): void
    {
        $fileName = $path.'/'.$name.'.json';

        file_put_contents(
            $fileName,
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
