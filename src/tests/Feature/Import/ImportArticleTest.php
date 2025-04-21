<?php

declare(strict_types=1);

namespace Tests\Feature\Import;

use App\Factories\WPConnectorFactory;
use App\Services\Importing\ImageDownloader;
use DateTime;
use Tests\Support\MockConnectorBuilder;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\AttachmentData;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\FullPostData;

test('article import is working', function () {
    $mockConnectorBuilder = new MockConnectorBuilder($this->app);
    $mockConnectorBuilder->mockGetPost(function (int $postId) {
        $postData = new FullPostData;
        $postData->id = 666;
        $postData->url = 'https://example.com/imported-article';
        $postData->status = 'published';
        $postData->title = 'Imported Article';
        $postData->author = 'test';
        $postData->content = 'test';
        $postData->category = 'test';
        $postData->publishedAt = new DateTime;
        $postData->modifiedAt = new DateTime;

        return $postData;
    });
    $mockConnectorBuilder->mockGetPostThumbnail(function (int $postId) {
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
    $this->mock(ImageDownloader::class, function ($mock) {
        $mock->shouldReceive('downloadPreview')->andReturn();
    });

    $this->artisan('import-article', ['id' => '666']);

    $this->assertFileExists(config('app.vault_path').'/articles/666. Imported Article/original.json');
    $this->assertFileExists(config('app.vault_path').'/articles/666. Imported Article/original.md');
});
