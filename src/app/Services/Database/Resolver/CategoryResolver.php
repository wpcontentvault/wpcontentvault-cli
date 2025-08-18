<?php

declare(strict_types=1);

namespace App\Services\Database\Resolver;

use App\Models\Category;
use App\Models\CategoryLocalization;
use App\Models\Locale;

class CategoryResolver
{
    public function __construct() {}

    public function resolveCategoryByName(?string $categoryName, Locale $locale): ?Category
    {
        if ($categoryName === null) {
            return null;
        }

        /** @var CategoryLocalization|null $localization */
        $localization = CategoryLocalization::query()
            ->where('locale_id', $locale->getKey())
            ->where('name', $categoryName)
            ->first();

        if ($localization === null) {
            return null;
        }

        return $localization->category;
    }

    public function resolveCategoryBySlug(?string $categorySlug): ?Category
    {
        if ($categorySlug === null) {
            return null;
        }

        return Category::query()
            ->where('slug', $categorySlug)
            ->first();
    }
}
