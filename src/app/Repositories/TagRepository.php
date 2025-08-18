<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
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

    public function getMatchableTags(): Collection
    {
        return $this->createQuery()
            ->where('is_stale', false)
            ->whereHas('category', function (Builder $query) {
                $query->where('is_hidden', false);
            })
            ->get();
    }

    public function findTagBySlug(string $slug): ?Tag
    {
        return $this->createQuery()
            ->where('slug', $slug)
            ->first();
    }

    public function findTagByUuid(string $uuid): ?Tag
    {
        return $this->createQuery()
            ->where('id', $uuid)
            ->first();
    }

    public function getStaleQuery(): ?Builder
    {
        return $this->createQuery()
            ->where('is_stale', true);
    }

    protected function getModelName(): string
    {
        return Tag::class;
    }
}
