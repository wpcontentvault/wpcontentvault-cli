<?php

declare(strict_types=1);

namespace Tests\Unit\Converters\GutenbergToArray;

use App\Configuration\WordpressConfiguration;
use Mockery\MockInterface;

it('can render gutenberg table block', function () {
    $config = \Mockery::mock(WordpressConfiguration::class, function (MockInterface $mock) {});

    $block = new \App\Blocks\Gutenberg\Table("<table></table>");

    $result = $block->render($config);

    $this->assertIsArray($result);
});
