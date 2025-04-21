<?php

declare(strict_types=1);

namespace App\Services\Vault\Meta;

class ImportChecksumMeta extends MetadataManager
{
    public function writeImportChecksum(string $path, string $name, string $value): void
    {
        $filePath = $this->resolveImportChecksumFilePath($path, $name);

        file_put_contents(
            $filePath,
            $value
        );
    }

    public function readImportChecksum(string $path, string $name): ?string
    {
        $filePath = $this->resolveImportChecksumFilePath($path, $name);

        if (file_exists($filePath) === false) {
            return null;
        }

        return file_get_contents(
            $filePath
        );
    }

    public function resolveImportChecksumFilePath(string $path, string $name): ?string
    {
        return $this->resolveFilePath($path, 'import', $name.'_sum.txt');
    }
}
