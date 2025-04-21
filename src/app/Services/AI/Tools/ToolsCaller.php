<?php

declare(strict_types=1);

namespace App\Services\AI\Tools;

use App\Context\AI\Tools\ToolFunction;

class ToolsCaller
{
    public function call(ToolFunction $function, array $arguments)
    {
        return $function->getCallable()(...$arguments);
    }
}
