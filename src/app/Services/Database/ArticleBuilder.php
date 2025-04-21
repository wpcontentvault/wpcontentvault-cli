<?php

declare(strict_types=1);

namespace App\Services\Database;

use App\Models\Article;
use App\Models\Locale;
use App\Models\Paragraph;
use App\Repositories\ParagraphRepository;

class ArticleBuilder
{
    public function __construct(
        private ParagraphRepository $paragraphs,
    ) {}

    public function buildTranslation(Article $article, Locale $locale): void
    {
        $collection = $this->paragraphs->findParagraphsByArticle($article);

        $content = '';

        foreach ($collection as $index => $paragraph) {
            /** @var Paragraph $paragraph */
            if ($paragraph->type === 'App\Components\Block\Gutenberg\Separator') {
                continue;
            }

            $content .= $paragraph->getContent($locale)."\n\n";
        }

        file_put_contents($article->path.'/'.$locale->code.'.md', $content);
    }
}
