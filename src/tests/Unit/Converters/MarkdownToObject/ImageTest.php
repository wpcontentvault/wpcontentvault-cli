<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse standalone image', function () {
    $content = <<<'STR'
![Alt text](https://example.com/image.jpg)
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();

    $this->assertCount(1, $children);
    /** @var \App\Blocks\Object\ImageObject $image */
    $image = $children[0];

    $this->assertSame(BlockTypeEnum::IMAGE->value, $image->getType());
    $this->assertSame('Alt text', $image->getAttributes()['alt']);
    $this->assertSame('https://example.com/image.jpg', $image->getAttributes()['src']);
});
