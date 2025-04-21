<?php

declare(strict_types=1);

namespace App\Services\Linting\Linters;

class ImageWithoutLineBreaksLinter extends AbstractLinter
{
    public function check(string $content): bool
    {
        $patternBefore = "/[^\n ][\n]{0,1} {0,1}\!\[\]\([^)]+[^)]+\)/u";
        $patternAfter = "/\!\[\]\([^)]+[^)]+\)[\n]{0,1}[^\n]/u";

        return preg_match($patternBefore, $content) != false || preg_match($patternAfter, $content) != false;
    }

    public function getErrorMessage(): string
    {
        return 'Image without linebreaks';
    }
}
