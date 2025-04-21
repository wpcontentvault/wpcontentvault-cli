<?php

declare(strict_types=1);

namespace App\Services\Importing;

use App\Contracts\Wordpress\ImageDownloaderInterface;
use App\Services\Utils\StringUtils;
use ErrorException;

class ImageDownloader implements ImageDownloaderInterface
{
    public function downloadPreview(string $url, string $path, string $name): void
    {
        $extension = pathinfo($url, PATHINFO_EXTENSION);

        if ($extension !== 'png') {
            $this->saveAndConvertToPng($url, $path, $name);
        } else {
            file_put_contents($path.'/'.$name.'.'.$extension, file_get_contents($url));
        }
    }

    public function downloadMedia(string $url, string $path): string
    {
        if (str_starts_with($url, '//')) {
            $url = 'https:'.$url;
        }

        $extension = pathinfo($url, PATHINFO_EXTENSION);

        $originalUrl = StringUtils::removeImageSize($url);
        $name = pathinfo($originalUrl, PATHINFO_FILENAME);

        if ($extension === 'webp') {
            if (file_exists($path.'/'.$name.'.png')) {
                return $name.'.png';
            }

            $imagePath = $path.'/';
            $this->saveAndConvertToPng($originalUrl, $imagePath, $name);

            return $name.'.png';
        }
        $imageFile = $path.'/'.$name.'.'.$extension;

        if (file_exists($imageFile)) {
            return $name.'.'.$extension;
        }

        $this->saveImageAsIs($originalUrl, $url, $imageFile);

        return $name.'.'.$extension;

    }

    private function saveImageAsIs(string $url, string $fallback, string $file): void
    {
        try {
            file_put_contents($file, file_get_contents($url));
        } catch (ErrorException $e) {
            // Some images may contain size in original name
            if (str_contains($e->getMessage(), 'Failed to open stream: HTTP request failed! HTTP/1.1 404 Not Found')) {
                file_put_contents($file, file_get_contents($fallback));
            } else {
                throw $e;
            }
        }
    }

    private function saveAndConvertToPng(string $url, string $path, string $name): void
    {
        $tmpImagePath = '/tmp/image.webp';

        file_put_contents($tmpImagePath, file_get_contents($url));

        $im = \imagecreatefromwebp($tmpImagePath);
        imagepng($im, $path.'/'.$name.'.png');

        imagedestroy($im);
        unlink($tmpImagePath);
    }
}
