<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Enum\GutenbergBlogTypeEnum;
use App\Models\Article;
use App\Models\Locale;
use App\Models\Paragraph;
use App\Repositories\ParagraphRepository;

it("can get last heading for article", function () {
    $locale = Locale::query()->first();

    $article = Article::factory()
        ->locale($locale)
        ->create();

    Paragraph::factory()
        ->type(GutenbergBlogTypeEnum::PARAGRAPH)
        ->order(1)
        ->article($article)
        ->create();

    Paragraph::factory()
        ->type(GutenbergBlogTypeEnum::HEADING)
        ->order(2)
        ->article($article)
        ->create();

    Paragraph::factory()
        ->type(GutenbergBlogTypeEnum::PARAGRAPH)
        ->order(3)
        ->article($article)
        ->create();

    $lastHeading = Paragraph::factory()
        ->type(GutenbergBlogTypeEnum::HEADING)
        ->order(4)
        ->article($article)
        ->create();

    Paragraph::factory()
        ->type(GutenbergBlogTypeEnum::HEADING)
        ->order(5)
        ->stale()
        ->article($article)
        ->create();

    $paragraphRepository = app(ParagraphRepository::class);
    $last = $paragraphRepository->getLastHeaderForArticle($article);

    $this->assertSame($lastHeading->id, $last->id);
});
