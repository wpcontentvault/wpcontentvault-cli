<?php

declare(strict_types=1);

namespace App\Services\Database\Resolver;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\TagLocalization;

class TagResolver
{
    public function resolveTagByName(?string $tagName, Locale $locale): ?Tag
    {
        if ($tagName === null) {
            return null;
        }

        /** @var TagLocalization|null $localization */
        $localization = TagLocalization::query()
            ->where('locale_id', $locale->getKey())
            ->where('name', $tagName)
            ->first();

        if ($localization === null) {
            return null;
        }

        return $localization->tag;
    }

    public function resolveTagBySlug(?string $tagSlug): ?Tag
    {
        if ($tagSlug === null) {
            return null;
        }

        return Tag::query()
            ->where('slug', $tagSlug)
            ->first();
    }
}
