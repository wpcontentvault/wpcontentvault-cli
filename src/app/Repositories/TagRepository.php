<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Collection;

/**
 * Class TagRepository
 *
 * @extends AbstractRepository<Tag>
 */
class TagRepository extends AbstractRepository
{
    public function getAllTags(): Collection
    {
        return $this->createQuery()
            ->get();
    }

    public function findTagBySlug(string $slug): ?Tag
    {
        return $this->createQuery()
            ->where('slug', $slug)
            ->first();
    }

    protected function getModelName(): string
    {
        return Tag::class;
    }
}
