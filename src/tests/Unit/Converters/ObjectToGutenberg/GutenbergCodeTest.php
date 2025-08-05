<?php

declare(strict_types=1);

namespace Tests\Unit\Converters\ObjectToGutenberg;

use App\Enum\BlockTypeEnum;
use App\Services\Converters\ObjectToGutenberg\ObjectToGutenbergConverter;

it('converts code object to block', function () {
    $code = new \App\Blocks\Object\CodeObject([], collect(), 'test \n\n test');

    $converter = new ObjectToGutenbergConverter(new \App\Services\Converters\ObjectToGutenberg\HtmlRenderer);

    $result = $converter->convert(collect([$code]));

    $this->assertTrue(str_contains($result[0]->getContent(), '\n\n'));
    $this->assertInstanceOf(\App\Blocks\Gutenberg\Code::class, $result[0]);
});
