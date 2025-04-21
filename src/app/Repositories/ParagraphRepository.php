<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\GutenbergBlogTypeEnum;
use App\Models\Article;
use App\Models\Paragraph;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class ParagraphRepository
 *
 * @extends AbstractRepository<Paragraph>
 */
class ParagraphRepository extends AbstractRepository
{
    public function findParagraphByHashAndArticle(string $hash, Article $article): ?Paragraph
    {
        return $this->createQuery()
            ->where('hash', $hash)
            ->where('article_id', $article->getKey())
            ->first();
    }

    public function findParagraphByUuid(string $uuid): Paragraph
    {
        return $this->createQuery()
            ->where('id', $uuid)
            ->first();
    }

    public function findParagraphByUuidAndArticle(string $uuid, Article $article): ?Paragraph
    {
        return $this->createQuery()
            ->where('id', $uuid)
            ->where('article_id', $article->getKey())
            ->first();
    }

    public function findParagraphsByArticle(Article $article): Collection
    {
        return $this->createQuery()
            ->where('article_id', $article->getKey())
            ->where('is_stale', false)
            ->with('translations')
            ->orderBy('order', 'asc')
            ->get();
    }

    public function getLastHeaderForArticle(Article $article): ?Paragraph
    {
        return $this->createQuery()
            ->where('article_id', $article->getKey())
            ->with('translations')
            ->where('is_stale', false)
            ->orderBy('order', 'desc')
            ->where('type', GutenbergBlogTypeEnum::HEADING->value)
            ->first();
    }

    /**
     * @return Builder<Paragraph>
     */
    public function getStaleQuery(): ?Builder
    {
        return $this->createQuery()
            ->where('is_stale', true);
    }

    protected function getModelName(): string
    {
        return Paragraph::class;
    }
}
