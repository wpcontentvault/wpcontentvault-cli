<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse simple paragraph content', function () {
    $content = <<<'STR'
This is a simple paragraph with plain text.
STR;
    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();

    $this->assertCount(1, $children);
    /** @var \App\Blocks\Object\TextObject $text */
    $text = $children[0];

    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('This is a simple paragraph with plain text.', $text->getContent());
});
