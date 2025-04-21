<?php

declare(strict_types=1);

namespace App\Services\Vault;

use Illuminate\Support\Str;

class VaultConfigWriter
{
    public function writeToPath(string $path, string $name, array $json): void
    {
        $file = Str::finish($path, '/').$name;

        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT));
    }
}
