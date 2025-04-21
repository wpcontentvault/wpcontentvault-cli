<?php

declare(strict_types=1);

namespace App\Enum\Traits;

trait InteractsWithValues
{
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
}
