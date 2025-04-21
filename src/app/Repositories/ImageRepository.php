<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use App\Models\Image;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ImageRepository
 *
 * @extends AbstractRepository<Image>
 */
class ImageRepository extends AbstractRepository
{
    public function findImageByHashAndArticle(string $hash, Article $article): ?Image
    {
        return $this->createQuery()
            ->where('hash', $hash)
            ->where('article_id', $article->getKey())
            ->first();
    }

    public function findImageByUuid(string $uuid): ?Image
    {
        return $this->createQuery()
            ->where('id', $uuid)
            ->first();
    }

    public function findImageByUuidAndArticle(string $uuid, Article $article): ?Image
    {
        return $this->createQuery()
            ->where('id', $uuid)
            ->where('article_id', $article->getKey())
            ->first();
    }

    /**
     * @return Builder<Image>|null
     */
    public function getStaleQuery(): ?Builder
    {
        return $this->createQuery()
            ->where('is_stale', true);
    }

    protected function getModelName(): string
    {
        return Image::class;
    }
}
