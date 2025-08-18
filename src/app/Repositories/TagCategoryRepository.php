<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TagCategory;
use Illuminate\Database\Eloquent\Builder;
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

    public function getMatchableTagCategories(): Collection
    {
        return $this->createQuery()
            ->with('tags')
            ->where('is_hidden', false)
            ->get();
    }

    public function findTagCategoryBySlug(string $slug): ?TagCategory
    {
        return $this->createQuery()
            ->where('slug', $slug)
            ->first();
    }

    public function getStaleQuery(): ?Builder
    {
        return $this->createQuery()
            ->where('is_stale', true);
    }

    protected function getModelName(): string
    {
        return TagCategory::class;
    }
}
