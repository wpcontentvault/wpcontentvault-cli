<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse markdown link content', function () {
    $content = <<<'STR'
[Example Link](https://example.com)
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();

    $this->assertCount(1, $children);
    /** @var \App\Blocks\Object\LinkObject $link */
    $link = $children[0];

    $this->assertSame(BlockTypeEnum::LINK->value, $link->getType());
    $this->assertSame('Example Link', $link->getChildren()[0]->getContent());
    $this->assertSame('https://example.com', $link->getAttributes()['href']);
});

