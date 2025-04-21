<?php

declare(strict_types=1);

namespace App\Configuration;

use Illuminate\Support\Str;

class DataPathConfiguration
{
    public static function getDataPath(): string
    {
        return Str::finish(env('WPCONTENTVAULT_DATA_PATH', getcwd().'/data'), '/');
    }
}
