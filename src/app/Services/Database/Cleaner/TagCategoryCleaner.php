<?php

declare(strict_types=1);

namespace App\Services\Database\Cleaner;

use App\Models\Tag;
use App\Repositories\TagCategoryRepository;
use App\Repositories\TagRepository;

class TagCategoryCleaner
{
    public function __construct(
        private TagCategoryRepository $categories
    ) {}

    public function markCategoriesAsStale(array $ids): void
    {
        foreach ($ids as $id) {
            $text = $this->categories->findTagCategoryBySlug($id);
            $text->is_stale = true;
            $text->save();
        }
    }

    public function removeStaleCategories(): void
    {
        $this->categories->getStaleQuery()->each(function (Tag $tag): void {
            $tag->delete();
        });
    }
}
