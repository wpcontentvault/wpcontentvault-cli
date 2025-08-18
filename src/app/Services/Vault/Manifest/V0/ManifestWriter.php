<?php

declare(strict_types=1);

namespace App\Services\Vault\Manifest\V1;

use App\Context\Markdown\PostMeta;

class ManifestWriter
{
    public function writeManifest(string $path, string $name, PostMeta $meta): void
    {
        if ($meta->category !== null) {
            $categoryLocalization = $meta->category->findLocalizationByLocale($meta->locale);
        } else {
            $categoryLocalization = null;
        }

        $data = [
            'version' => 1,
            'locale' => $meta->locale->code,
            'title' => $meta->title,
            'status' => $meta->status,
            'author' => $meta->author,
            'published_at' => $meta->publishedAt?->format('Y-m-d H:i:s'),
            'modified_at' => $meta->modifiedAt?->format('Y-m-d H:i:s'),
            'url' => $meta->url,
            'external_id' => $meta->externalId,
            'category' => $categoryLocalization?->name,
            'tags' => $meta->tags,
        ];

        file_put_contents(
            $path.'/'.$name.'.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
