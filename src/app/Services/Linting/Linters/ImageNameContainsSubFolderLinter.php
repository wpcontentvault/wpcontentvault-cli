<?php

declare(strict_types=1);

namespace App\Services\Linting\Linters;

class ImageNameContainsSubFolderLinter
{
    public function check(string $content): bool
    {
        $pattern = "/\!\[\]\([^)]+\/[^)]+\)/u";

        return preg_match($pattern, $content) != false;
    }

    public function getErrorMessage(): string
    {
        return 'Image path path contains sub folders';
    }
}
