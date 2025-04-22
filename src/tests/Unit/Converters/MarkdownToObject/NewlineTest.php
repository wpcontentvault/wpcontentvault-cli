<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse paragraphs with newline block between them', function () {
    $content = <<<'STR'
This is the first paragraph.

This is the second paragraph.
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    // Check first paragraph
    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();
    $this->assertCount(1, $children);
    /** @var \App\Blocks\Object\TextObject $text */
    $text = $children[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('This is the first paragraph.', $text->getContent());

    // Check newline block
    $this->assertSame($result[1]->getType(), BlockTypeEnum::NEWLINE->value);

    // Check second paragraph
    $this->assertSame($result[2]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[2]->getChildren();
    $this->assertCount(1, $children);
    $text = $children[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('This is the second paragraph.', $text->getContent());
}); 