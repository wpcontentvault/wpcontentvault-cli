<?php

declare(strict_types=1);

it('can render quotes as object block', function () {
    $content = <<<'STR'
> test
> test
> test
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), \App\Enum\BlockTypeEnum::QUOTE->value);
});
