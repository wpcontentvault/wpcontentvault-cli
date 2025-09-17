<?php

declare(strict_types=1);

namespace App\Services\Summarization;

use App\Models\Article;

class ArticleSummarizer
{
    public function __construct(
        private SummarizationService $summarizationService,
    ) {}

    public function summarizeArticle(Article $article): void
    {
        $contextFile = $article->path . 'context.md';

        if (file_exists($contextFile)) {
            return;
        }

        $content = file_get_contents($article->path . 'original.md');

        if (mb_strlen($content) > 12_000) {
            $content = mb_substr($content, 0, 12_000);
        }

        $summarize = $this->summarizationService->summarizeArticle($content);

        file_put_contents($contextFile, $summarize->summary);
    }
}
