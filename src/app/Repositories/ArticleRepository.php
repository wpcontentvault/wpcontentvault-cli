<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Support\Collection;

/**
 * Class ArticleRepository
 *
 * @extends AbstractRepository<Article>
 */
class ArticleRepository extends AbstractRepository
{
    public function getAllArticles(): Collection
    {
        return Article::all();
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
