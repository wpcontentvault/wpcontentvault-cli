<?php

declare(strict_types=1);

namespace Tests\Unit\Converters\GutenbergToArray;

use App\Blocks\Gutenberg\Paragraph;
use App\Configuration\WordpressConfiguration;
use Mockery\MockInterface;

it('can render gutenberg quote block', function () {
    $config = \Mockery::mock(WordpressConfiguration::class, function (MockInterface $mock) {});

    $block = new \App\Blocks\Gutenberg\Quote(null, collect([
        new Paragraph('test')
    ]));

    $result = $block->render($config);

    $this->assertIsArray($result);
});
