<?php

declare(strict_types=1);

namespace App\Configuration;

class StorageConfiguration
{
    public static function getLogsPath(string $file): string
    {
        $basePath = DataPathConfiguration::getDataPath();

        $storagePath = $basePath.'storage/logs/';

        if (file_exists($storagePath) === false) {
            mkdir($storagePath, 0755, true);
        }

        return $storagePath.$file;
    }
}
