<?php

declare(strict_types=1);

namespace App\Services\Vault;

use Illuminate\Support\Str;

class VaultPathResolver
{
    public function getRoot(): string
    {
        return Str::finish(\config('app.vault_path'), '/');
    }

    public function getArticlesRoot(): string
    {
        return $this->getRoot().Str::finish('articles', '/');
    }

    public function resolveArticlePath(string $path): string
    {
        $path = Str::ltrim($path, '/');

        return $this->getArticlesRoot().Str::finish($path, '/');
    }
}
