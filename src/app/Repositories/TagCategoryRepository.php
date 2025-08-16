<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TagCategory;
use Illuminate\Support\Collection;

/**
 * Class TagCategoryRepository
 *
 * @extends AbstractRepository<TagCategory>
 */
class TagCategoryRepository extends AbstractRepository
{
    public function getAllTagCategories(): Collection
    {
        return $this->createQuery()
            ->with('tags')
            ->get();
    }

    public function findTagCategoryBySlug(string $slug): ?TagCategory
    {
        return $this->createQuery()
            ->where('slug', $slug)
            ->first();
    }

    protected function getModelName(): string
    {
        return TagCategory::class;
    }
}
