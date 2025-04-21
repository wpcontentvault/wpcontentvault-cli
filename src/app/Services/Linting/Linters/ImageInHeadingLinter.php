<?php

declare(strict_types=1);

namespace App\Services\Linting\Linters;

class ImageInHeadingLinter extends AbstractLinter
{
    public function check(string $content): bool
    {
        $pattern = "/\#[^!^\n]*\!\[\]\([^)]+\)/u";

        return preg_match($pattern, $content) != false;
    }

    public function getErrorMessage(): string
    {
        return 'Image is marked as a heading';
    }
}
