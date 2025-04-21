<?php

declare(strict_types=1);

namespace App\Services\Importing;

use App\Contracts\Wordpress\ImageDownloaderInterface;
use App\Services\Utils\StringUtils;
use RuntimeException;

class NopImageDownloader implements ImageDownloaderInterface
{
    public function downloadMedia(string $url, string $path): string
    {
        $extension = pathinfo($url, PATHINFO_EXTENSION);

        $originalUrl = StringUtils::removeImageSize($url);
        $name = pathinfo($originalUrl, PATHINFO_FILENAME);

        $imageFile = $path.'/'.$name.'.'.$extension;

        if (file_exists($imageFile)) {
            return $name.'.'.$extension;
        }

        throw new RuntimeException("Not supposed to download anything! Tries to download $url");
    }
}
