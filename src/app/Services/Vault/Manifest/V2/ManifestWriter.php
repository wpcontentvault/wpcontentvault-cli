<?php

declare(strict_types=1);

namespace App\Services\Vault\Manifest\V2;

use App\Context\Markdown\PostMeta;

class ManifestWriter
{
    public function writeManifest(string $path, string $name, PostMeta $meta): void
    {
        if ($name === 'original') {
            $sharedData = [
                'version' => '2',
                'category' => $meta->category?->slug,
                'tags' => collect($meta->tags)->pluck('slug')->toArray(),
            ];

            file_put_contents(
                $path . '/attrs.json',
                json_encode($sharedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }


        $data = [
            'version' => 2,
            'locale' => $meta->locale->code,
            'title' => $meta->title,
            'status' => $meta->status,
            'author' => $meta->author,
            'published_at' => $meta->publishedAt?->format('Y-m-d H:i:s'),
            'modified_at' => $meta->modifiedAt?->format('Y-m-d H:i:s'),
            'url' => $meta->url,
            'external_id' => $meta->externalId,

        ];

        file_put_contents(
            $path . '/' . $name . '.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
