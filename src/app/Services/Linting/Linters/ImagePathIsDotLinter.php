<?php

declare(strict_types=1);

namespace App\Services\Linting\Linters;

class ImagePathIsDotLinter extends AbstractLinter
{
    public function check(string $content): bool
    {
        return str_contains($content, '![](.)');
    }

    public function getErrorMessage(): string
    {
        return 'Image contains dot instead of path';
    }
}
