<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Collection;

/**
 * Class LocaleRepository
 *
 * @extends AbstractRepository<Category>
 */
class CategoryRepository extends AbstractRepository
{
    public function getAllCategories(): Collection
    {
        return $this->createQuery()
            ->get();
    }

    public function findCategoryBySlug(string $slug): ?Category
    {
        return $this->createQuery()
            ->where('slug', $slug)
            ->first();
    }

    protected function getModelName(): string
    {
        return Category::class;
    }
}
