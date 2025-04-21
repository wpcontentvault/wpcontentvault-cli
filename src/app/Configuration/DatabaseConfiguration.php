<?php

declare(strict_types=1);

namespace App\Configuration;

class DatabaseConfiguration
{
    public static function resolveDatabaseFile(string $fileName): string
    {
        $basePath = DataPathConfiguration::getDataPath();

        $databasePath = $basePath.'database/';

        if (file_exists($databasePath) === false) {
            mkdir($databasePath, 0775, true);
        }

        $file = $databasePath.$fileName;

        if (file_exists($file) === false) {
            // Create a new database
            touch($file);
        }

        return $file;
    }
}
