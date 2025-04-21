<?php

declare(strict_types=1);

namespace App\Contracts\Console;

interface ApplicationOutputInterface
{
    public function info(string $message);

    public function error(string $message);
}
