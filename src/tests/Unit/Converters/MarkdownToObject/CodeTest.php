<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse terminal content', function () {
    $content = <<<'STR'
```
test
test
test
```
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::CODE->value);
});

it('does handle backslash correctly', function () {
    $content = <<<'STR'
```
test\n\n test
```
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::CODE->value);
    $this->assertTrue(str_contains($result[0]->getContent(), '\n\n'));
});
