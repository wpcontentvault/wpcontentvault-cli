<?php

declare(strict_types=1);

namespace App\Services\Vault;

use Illuminate\Support\Str;
use RuntimeException;

class VaultConfigLoader
{
    public function configExists(string $path, string $name): bool
    {
        $file = Str::finish($path, '/').$name;

        return file_exists($file);
    }

    public function loadFromPath(string $path, string $name): array
    {
        $file = Str::finish($path, '/').$name;

        if (file_exists($file) === false) {
            throw new RuntimeException("Can't load config file $file, file does not exist");
        }

        $data = file_get_contents($file);

        if (json_validate($data) === false) {
            throw new RuntimeException("Can't deserialize data from $file");
        }

        return json_decode($data, true);
    }
}
