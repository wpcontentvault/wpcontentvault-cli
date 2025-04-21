<?php

declare(strict_types=1);

namespace App\Services\Utils;

use FilesystemIterator;
use InvalidArgumentException;

class FilesystemUtils
{
    public static function isFolderEmpty($folderPath): bool
    {
        if (! is_dir($folderPath)) {
            throw new InvalidArgumentException("The specified path is not a directory: $folderPath");
        }

        $iterator = new FilesystemIterator($folderPath);

        // If the iterator doesn't have any elements, the folder is empty
        return ! $iterator->valid();
    }
}
