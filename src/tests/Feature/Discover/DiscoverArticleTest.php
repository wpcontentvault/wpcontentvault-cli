<?php

declare(strict_types=1);

namespace Tests\Feature\Discover;

use App\Factories\WPConnectorFactory;
use App\Models\Article;
use App\Models\Locale;
use Tests\Support\MockConnectorBuilder;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\FullPostData;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\PostData;

test('article discovery for not created article is working', function () {
    $mockConnectorBuilder = new MockConnectorBuilder($this->app);
    $mockConnectorBuilder->mockAddPost(function (string $title, string $content) {
        $postData = new PostData;
        $postData->id = 123;
        $postData->title = $title;
        $postData->url = 'https://example.com/example-post';

        return $postData;
    });
    $mockConnector = $mockConnectorBuilder->build();

    $this->mock(WPConnectorFactory::class, function ($mock) use ($mockConnector) {
        $mock->shouldReceive('make')->andReturn($mockConnector);
    });

    $this->artisan('discover-article-from-path', ['path' => 'discover-article-not-published']);

    $this->assertDatabaseHas(
        (new Article)->getTable(), ['external_id' => 123]
    );

    $this->assertFileExists(config('app.vault_path').'/articles/discover-article-not-published/article_id.txt');
});

test('article discovery for already created article is working', function () {
    $mockConnectorBuilder = new MockConnectorBuilder($this->app);
    $mockConnectorBuilder->mockGetPost(function (int $postId) {
        $postData = new FullPostData;
        $postData->id = 444;
        $postData->url = 'https://example.com/discover-article-published';
        $postData->status = 'published';
        $postData->title = 'Example Published';
        $postData->author = 'test';

        return $postData;
    });
    $mockConnector = $mockConnectorBuilder->build();

    $this->mock(WPConnectorFactory::class, function ($mock) use ($mockConnector) {
        $mock->shouldReceive('make')->andReturn($mockConnector);
    });

    $this->artisan('discover-article-from-path', ['path' => 'discover-article-published']);

    $this->assertDatabaseHas(
        (new Article)->getTable(), [
            'external_id' => 444,
            'url' => 'https://example.com/discover-article-published',
        ]
    );

    $this->assertFileExists(config('app.vault_path').'/articles/discover-article-published/article_id.txt');
});

test('article discovery for already discovered article is working', function () {
    $mockConnectorBuilder = new MockConnectorBuilder($this->app);
    $mockConnectorBuilder->mockGetPost(function (int $postId) {
        $postData = new FullPostData;
        $postData->id = 555;
        $postData->url = 'https://example.com/discover-article-discovered';
        $postData->status = 'published';
        $postData->title = 'Example Discovered';
        $postData->author = 'test';

        return $postData;
    });
    $mockConnector = $mockConnectorBuilder->build();

    $article = Article::factory()->create([
        'external_id' => 555,
        'path' => '/tmp/vault/articles/discover-article-discovered/',
        'locale_id' => Locale::whereCode('en')->first()->getKey(),
    ]);

    $this->mock(WPConnectorFactory::class, function ($mock) use ($mockConnector) {
        $mock->shouldReceive('make')->andReturn($mockConnector);
    });

    $this->artisan('discover-article-from-path', ['path' => 'discover-article-discovered']);

    $this->assertDatabaseCount($article->getTable(), 1);
});
