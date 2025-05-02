<?php

declare(strict_types=1);

namespace Tests\Unit\Converters\GutenbergToArray;

use App\Blocks\Gutenberg\ListItem;
use App\Configuration\WordpressConfiguration;
use Mockery\MockInterface;

it('can render gutenberg list block', function () {
    $config = \Mockery::mock(WordpressConfiguration::class, function (MockInterface $mock) {});

    $block = new \App\Blocks\Gutenberg\ListBlock(null, [
        new ListItem('test 1'),
        new ListItem('test 2'),
        new ListItem('test 3')
    ]);

    $result = $block->render($config);

    $this->assertIsArray($result);
});
