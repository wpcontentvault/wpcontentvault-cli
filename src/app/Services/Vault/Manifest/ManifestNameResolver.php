<?php

declare(strict_types=1);

namespace App\Services\Vault\Manifest;

use App\Models\Article;
use App\Models\Locale;

class ManifestNameResolver
{
    public function resolveName(Article $article, Locale $locale): string
    {
        if ($article->locale->code === $locale->code) {
            return 'original';
        }

        return $locale->code;
    }
}
