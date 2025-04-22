<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse italic text in paragraph', function () {
    $content = <<<'STR'
This is *italic* text
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();

    $this->assertCount(3, $children);

    // Check text before emphasis
    /** @var \App\Blocks\Object\TextObject $text */
    $text = $children[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('This is ', $text->getContent());

    // Check italic text
    /** @var \App\Blocks\Object\TextObject $italic */
    $italic = $children[1];
    $this->assertSame(BlockTypeEnum::EMPHASIS->value, $italic->getType());
    $this->assertSame('italic', $italic->getChildren()[0]->getContent());

    // Check text after emphasis
    $text = $children[2];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame(' text', $text->getContent());
});

it('can parse bold text in paragraph', function () {
    $content = <<<'STR'
This is **bold** text
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();

    $this->assertCount(3, $children);

    // Check text before emphasis
    /** @var \App\Blocks\Object\TextObject $text */
    $text = $children[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('This is ', $text->getContent());

    // Check bold text
    /** @var \App\Blocks\Object\TextObject $bold */
    $bold = $children[1];
    $this->assertSame(BlockTypeEnum::STRONG->value, $bold->getType());
    $this->assertSame('bold', $bold->getChildren()[0]->getContent());

    // Check text after emphasis
    $text = $children[2];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame(' text', $text->getContent());
});

it('can parse multiple emphasis styles in paragraph', function () {
    $content = <<<'STR'
This is ***bold and italic***
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();

    $this->assertCount(2, $children);

    // Check text before emphasis
    /** @var \App\Blocks\Object\TextObject $text */
    $text = $children[0];
    $this->assertSame(BlockTypeEnum::TEXT->value, $text->getType());
    $this->assertSame('This is ', $text->getContent());

    // Check bold and italic text
    /** @var \App\Blocks\Object\TextObject $boldItalic */
    $boldItalic = $children[1];
    $this->assertSame(BlockTypeEnum::EMPHASIS->value, $boldItalic->getType());
    $this->assertSame(BlockTypeEnum::STRONG->value, $boldItalic->getChildren()[0]->getType());
    $text = $boldItalic->getChildren()[0]->getChildren()[0];
    $this->assertSame('bold and italic', $text->getContent());
});
