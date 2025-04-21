<?php

declare(strict_types=1);

namespace Tests\Feature\Export;

use App\Factories\WPConnectorFactory;
use App\Models\Article;
use App\Models\ArticleLocalization;
use App\Models\Locale;
use Tests\Support\MockConnectorBuilder;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\AttachmentData;

test('article export is working', function () {
    $mockConnectorBuilder = new MockConnectorBuilder($this->app);
    $mockConnectorBuilder->mockGetPostBlocks(function (int $postId) {
        return [];
    });
    $mockConnectorBuilder->mockSetPostBlocks(function (int $postId) {
        return 777;
    });
    $mockConnectorBuilder->mockSetPostThumbnail(function (int $postId) {
        return 777;
    });
    $mockConnectorBuilder->mockSetPostTitle(function (int $postId) {
        return 777;
    });
    $mockConnectorBuilder->mockSetPostCategory(function (int $postId) {
        return 777;
    });
    $mockConnectorBuilder->mockAddAttachment(function (string $imageName, string $imageData, ?int $post_id = null) {
        $postData = new AttachmentData;
        $postData->attachmentId = 9111;
        $postData->attachmentUrl = 'https://example.com/example.png';
        $postData->largeUrl = 'https://example.com/example-large.png';
        $postData->thumbnailUrl = 'https://example.com/example-thumb.png';

        return $postData;
    });
    $mockConnector = $mockConnectorBuilder->build();

    $this->mock(WPConnectorFactory::class, function ($mock) use ($mockConnector) {
        $mock->shouldReceive('make')->andReturn($mockConnector);
    });

    $article = Article::factory()->create([
        'external_id' => 777,
        'path' => '/tmp/vault/articles/export-article/',
        'locale_id' => Locale::whereCode('en')->first()->getKey(),
    ]);
    $localization = ArticleLocalization::factory()->create([
        'article_id' => $article->getKey(),
        'locale_id' => Locale::whereCode('en')->first()->getKey(),
        'external_id' => 777,
        'is_original' => true,
        'url' => 'http://example.com/articles/export-article/',
        'title' => 'Export Article',
    ]);
    file_put_contents($article->path.'/article_id.txt', $article->getKey());

    $this->artisan('upload-article', ['id' => '777']);

    $this->assertFileExists(config('app.vault_path').'/articles/export-article/_meta/export/original_sum.txt');
});
