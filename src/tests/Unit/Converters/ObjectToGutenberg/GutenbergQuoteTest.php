<?php

declare(strict_types=1);

use App\Services\Converters\ObjectToGutenberg\ObjectToGutenbergConverter;

it('can render quotes as gutenberg block', function () {
    $text1 = new \App\Blocks\Object\TextObject([], collect(), 'test 1');
    $newline1 = new \App\Blocks\Object\NewLineObject([], collect(), "\n");
    $text2 = new \App\Blocks\Object\TextObject([], collect(), 'test 2');
    $newline2 = new \App\Blocks\Object\NewLineObject([], collect(), "\n");
    $text3 = new \App\Blocks\Object\TextObject([], collect(), 'test 3');
    $children = collect([$text1, $newline1, $text2, $newline2, $text3]);
    $paragraph = new \App\Blocks\Object\ParagraphObject([], $children);
    $quote = new \App\Blocks\Object\QuoteObject([], collect([$paragraph]));

    $converter = new ObjectToGutenbergConverter(new \App\Services\Converters\ObjectToGutenberg\HtmlRenderer);

    $result = $converter->convert(collect([$quote]));

    $this->assertInstanceOf(\App\Blocks\Gutenberg\Quote::class, $result[0]);
});
