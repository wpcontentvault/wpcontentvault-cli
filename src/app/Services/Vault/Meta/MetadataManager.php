<?php

declare(strict_types=1);

namespace App\Services\Vault\Meta;

class MetadataManager
{
    protected function resolveFilePath(string $path, string $prefix, string $file): string
    {
        $filePath = $path.'/_meta/'.$prefix.'/'.$file;

        $dirPath = dirname($filePath);

        if (file_exists($dirPath) === false) {
            mkdir($dirPath, 0755, true);
        }

        return $filePath;
    }
}
