<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse standalone youtube link as embedded video', function () {
    $content = <<<'STR'
[Example YouTube Video](https://www.youtube.com/watch?v=dQw4w9WgXcQ)
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();

    $this->assertCount(1, $children);
    /** @var \App\Blocks\Object\VideoLinkObject $link */
    $link = $children[0];

    $this->assertSame(BlockTypeEnum::VIDEO_LINK->value, $link->getType());
    $this->assertSame('Example YouTube Video', $link->getChildren()[0]->getContent());
    $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $link->getAttributes()['href']);
});

it('can parse youtube link with text before and after as regular link', function () {
    $content = <<<'STR'
Check out this amazing [YouTube Video](https://www.youtube.com/watch?v=dQw4w9WgXcQ) tutorial
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::PARAGRAPH->value);
    $children = $result[0]->getChildren();

    $this->assertCount(3, $children);

    // Check text before link
    $this->assertSame(BlockTypeEnum::TEXT->value, $children[0]->getType());
    $this->assertSame('Check out this amazing ', $children[0]->getContent());

    // Check link
    /** @var \App\Blocks\Object\LinkObject $link */
    $link = $children[1];
    $this->assertSame(BlockTypeEnum::LINK->value, $link->getType());
    $this->assertSame('YouTube Video', $link->getChildren()[0]->getContent());
    $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $link->getAttributes()['href']);

    // Check text after link
    $this->assertSame(BlockTypeEnum::TEXT->value, $children[2]->getType());
    $this->assertSame(' tutorial', $children[2]->getContent());
});
