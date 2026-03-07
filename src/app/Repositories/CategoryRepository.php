<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class CategoryRepository
 *
 * @extends AbstractRepository<Category>
 */
class CategoryRepository extends AbstractRepository
{
    public function getAllCategories(): Collection
    {
        return $this->createQuery()
            ->where('is_stale', false)
            ->get();
    }

    public function findCategoryByUuid(string $id): ?Category
    {
        return $this->createQuery()
            ->where('id', $id)
            ->first();
    }

    public function findCategoryBySlug(string $slug): ?Category
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
        return Category::class;
    }
}
