<?php

declare(strict_types=1);

namespace App\Services\Database\Cleaner;

use App\Models\Article;
use App\Models\Paragraph;
use App\Repositories\ParagraphRepository;

class ParagraphCleaner
{
    public function __construct(
        private ParagraphRepository $paragraphs
    ) {}

    public function markParagraphsAsStale(array $ids): void
    {
        foreach ($ids as $id) {
            $text = $this->paragraphs->findParagraphByUuid($id);
            $text->is_stale = true;
            $text->save();
        }
    }

    public function removeStaleParagraphs(): void
    {
        $this->paragraphs->getStaleQuery()->each(function (Paragraph $paragraph): void {
            $paragraph->delete();
        });
    }

    public function removeTranslatedParagraphs(Article $article): void
    {
        $originalLocale = $article->locale;

        $this->paragraphs->createQuery()
            ->where('article_id', $article->getKey())
            ->whereNot('locale_id', $originalLocale->getKey())
            ->delete();
    }
}
