<?php

declare(strict_types=1);

namespace App\Services\Linting\Linters;

abstract class AbstractLinter
{
    abstract public function check(string $content): bool;
}
