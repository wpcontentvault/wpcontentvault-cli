<?php

declare(strict_types=1);

namespace App\Services\Linting\Linters;

class ImageNameContainsSizeLinter extends AbstractLinter
{
    public function check(string $content): bool
    {
        $pattern = "/\!\[\]\([^)]+-\d+[Xx]\d+\.[^)]+\)/";

        return preg_match($pattern, $content) != false;
    }

    public function getErrorMessage(): string
    {
        return 'Image path contains size';
    }
}
