<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse h1 heading', function () {
    $content = <<<'STR'
# This is a heading level 1
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::HEADING->value);
    $this->assertSame(1, $result[0]->getAttributes()['level']);

    $children = $result[0]->getChildren();
    $this->assertCount(1, $children);
    /** @var \App\Blocks\Object\TextObject $text */
    $text = $children[0];

    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('This is a heading level 1', $text->getContent());
});
