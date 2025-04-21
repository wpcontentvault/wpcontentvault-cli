<?php

declare(strict_types=1);

namespace App\Services\Vault\Meta;

use Illuminate\Support\Str;

class ArticleIdMeta
{
    public function writeSerializedId(string $path, string $value): void
    {
        $filePath = Str::finish($path, '/');

        file_put_contents($filePath.'article_id.txt', $value);
    }

    public function readSerializedId(string $path): ?string
    {
        $filePath = Str::finish($path, '/').'article_id.txt';

        if (file_exists($filePath) === false) {
            return null;
        }

        return file_get_contents($filePath);
    }
}
