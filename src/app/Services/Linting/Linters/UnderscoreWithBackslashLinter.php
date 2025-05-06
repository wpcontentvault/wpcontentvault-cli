<?php

declare(strict_types=1);

namespace App\Services\Linting\Linters;

class UnderscoreWithBackslashLinter extends AbstractLinter
{

    public function check(string $content): bool
    {
        return str_contains($content, '\_');
    }

    public function getErrorMessage(): string
    {
        return 'Underscore with backslash';
    }
}
