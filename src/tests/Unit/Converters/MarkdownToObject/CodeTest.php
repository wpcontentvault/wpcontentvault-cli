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
