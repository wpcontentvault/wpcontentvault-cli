<?php

declare(strict_types=1);

namespace Tests\Support;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FilesystemUtils
{
    public static function removeDirectory($dir): void
    {
        if (file_exists($dir) === false) {
            return;
        }

        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        rmdir($dir);
    }

    public static function copyDirectory(string $source, string $dest): void
    {
        mkdir($dest, 0755, true);

        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($dest.DIRECTORY_SEPARATOR.$iterator->getSubPathname());
            } else {
                copy($item->getPathname(), $dest.DIRECTORY_SEPARATOR.$iterator->getSubPathname());
            }
        }
    }
}
