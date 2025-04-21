<?php

declare(strict_types=1);

namespace App\Services\Linting\Linters;

class CodeBlocksWithoutLineBreakLinter extends AbstractLinter
{
    public function check(string $content): bool
    {
        $withoutNewLine = str_contains($content, '```````');
        $withOneNewLine = str_contains($content, "```\n```");
        $withOneNewLineAndSpace = str_contains($content, "``` \n```");

        return $withoutNewLine || $withOneNewLine || $withOneNewLineAndSpace;
    }

    public function getErrorMessage(): string
    {
        return 'Fenced code blocks without proper line break in between';
    }
}
