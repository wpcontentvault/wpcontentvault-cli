<?php

declare(strict_types=1);

namespace Tests\Unit\Converters\GutenbergToArray;

use App\Configuration\WordpressConfiguration;
use Mockery\MockInterface;

it('can render gutenberg Image block', function () {
    $config = \Mockery::mock(WordpressConfiguration::class, function (MockInterface $mock) {
        $mock->shouldReceive('isImageLightboxEnabled')->andReturn(false);
        $mock->shouldReceive('getImageAlign')->andReturn('center');
        $mock->shouldReceive('getImageLinkDestination')->andReturn('media');
    });

    $block = new \App\Blocks\Gutenberg\Image('https://example.com/image.jpg', '', 111);

    $result = $block->render($config);

    $this->assertIsArray($result);
});
