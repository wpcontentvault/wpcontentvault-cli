<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class ArticleRepository
 *
 * @extends AbstractRepository<Article>
 */
class ArticleRepository extends AbstractRepository
{
    public function searchArticles(string $query): Collection
    {
        return $this->createQuery()
            ->where('title', 'LIKE', '%' . $query . '%')
            ->get();
    }

    public function getAllArticles(): Collection
    {
        return Article::all();
    }

    public function getArticlesByYear(int $year): Collection
    {
        return $this->createQuery()
            ->where(function (Builder $builder) use ($year) {
                $builder->whereYear('published_at', $year)
                    ->orWhereYear('modified_at', $year);
            })
            ->get();
    }

    public function findArticleByPath(string $path): ?Article
    {
        return $this->createQuery()
            ->where('path', $path)
            ->first();
    }

    public function findArticleByUuid(string $id): ?Article
    {
        return $this->createQuery()
            ->where('id', $id)
            ->first();
    }

    public function findArticleByExternalId(int $id): ?Article
    {
        return $this->createQuery()
            ->where('external_id', $id)
            ->first();
    }

    protected function getModelName(): string
    {
        return Article::class;
    }
}
