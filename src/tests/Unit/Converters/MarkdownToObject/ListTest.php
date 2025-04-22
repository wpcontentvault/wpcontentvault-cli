<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse unordered list', function () {
    $content = <<<'STR'
- First item
- Second item
- Third item
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::LIST->value);

    $children = $result[0]->getChildren();
    //Three list items, two newlines
    $this->assertCount(5, $children);

    // Check first item
    /** @var \App\Blocks\Object\ListItemObject $item */
    $item = $children[0];
    $this->assertSame(BlockTypeEnum::LIST_ITEM->value, $item->getType());
    $this->assertCount(1, $item->getChildren());
    /** @var \App\Blocks\Object\TextObject $text */
    $text = $item->getChildren()[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('First item', $text->getContent());

    // Check second item
    $item = $children[2];
    $this->assertSame(BlockTypeEnum::LIST_ITEM->value, $item->getType());
    $this->assertCount(1, $item->getChildren());
    $text = $item->getChildren()[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('Second item', $text->getContent());

    // Check third item
    $item = $children[4];
    $this->assertSame(BlockTypeEnum::LIST_ITEM->value, $item->getType());
    $this->assertCount(1, $item->getChildren());
    $text = $item->getChildren()[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('Third item', $text->getContent());
});

it('can parse ordered list', function () {
    $content = <<<'STR'
1. First item
2. Second item
3. Third item
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::LIST->value);

    $children = $result[0]->getChildren();
    $this->assertCount(5, $children);

    // Check first item
    /** @var \App\Blocks\Object\ListItemObject $item */
    $item = $children[0];
    $this->assertSame(BlockTypeEnum::LIST_ITEM->value, $item->getType());
    $this->assertCount(1, $item->getChildren());
    /** @var \App\Blocks\Object\TextObject $text */
    $text = $item->getChildren()[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('First item', $text->getContent());

    // Check second item
    $item = $children[2];
    $this->assertSame(BlockTypeEnum::LIST_ITEM->value, $item->getType());
    $this->assertCount(1, $item->getChildren());
    $text = $item->getChildren()[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('Second item', $text->getContent());

    // Check third item
    $item = $children[4];
    $this->assertSame(BlockTypeEnum::LIST_ITEM->value, $item->getType());
    $this->assertCount(1, $item->getChildren());
    $text = $item->getChildren()[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('Third item', $text->getContent());
});

