<?php

declare(strict_types=1);

namespace App\Services\Vault\Meta;

class ExportChecksumMeta extends MetadataManager
{
    public function writeExportChecksum(string $path, string $name, string $value): void
    {
        $filePath = $this->resolveExportChecksumFilePath($path, $name);

        file_put_contents(
            $filePath,
            $value
        );
    }

    public function readExportChecksum(string $path, string $name): ?string
    {
        $filePath = $this->resolveExportChecksumFilePath($path, $name);

        if (file_exists($filePath) === false) {
            return null;
        }

        return file_get_contents($filePath);
    }

    public function resolveExportChecksumFilePath(string $path, string $name): ?string
    {
        return $this->resolveFilePath($path, 'export', $name.'_sum.txt');
    }
}
