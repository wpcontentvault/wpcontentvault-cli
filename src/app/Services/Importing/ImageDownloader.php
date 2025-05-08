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

        if ($extension === 'webp') {
            $this->saveWebpAndConvertToPng($url, $path, $name);
        } elseif ($extension === 'jpeg' || $extension === 'jpg') {
            $this->saveJpegAndConvertToPng($url, $path, $name);
        } elseif ($extension === 'png') {
            file_put_contents($path . '/' . $name . '.' . $extension, file_get_contents($url));
        } else {
            throw new \RuntimeException("Unknown image extension: $extension");
        }
    }

    public function downloadMedia(string $url, string $path): string
    {
        if (str_starts_with($url, '//')) {
            $url = 'https:' . $url;
        }

        $extension = pathinfo($url, PATHINFO_EXTENSION);

        $originalUrl = StringUtils::removeImageSize($url);
        $name = pathinfo($originalUrl, PATHINFO_FILENAME);

        if ($extension === 'webp') {
            if (file_exists($path . '/' . $name . '.png')) {
                return $name . '.png';
            }

            $imagePath = $path . '/';
            $this->saveWebpAndConvertToPng($originalUrl, $imagePath, $name);

            return $name . '.png';
        }
        $imageFile = $path . '/' . $name . '.' . $extension;

        if (file_exists($imageFile)) {
            return $name . '.' . $extension;
        }

        $this->saveImageAsIs($originalUrl, $url, $imageFile);

        return $name . '.' . $extension;

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

    private function saveWebpAndConvertToPng(string $url, string $path, string $name): void
    {
        $tmpImagePath = '/tmp/image.webp';

        file_put_contents($tmpImagePath, file_get_contents($url));

        $im = \imagecreatefromwebp($tmpImagePath);
        imagepng($im, $path . '/' . $name . '.png');

        imagedestroy($im);
        unlink($tmpImagePath);
    }

    private function saveJpegAndConvertToPng(string $url, string $path, string $name): void
    {
        $tmpImagePath = '/tmp/image.jpeg';

        file_put_contents($tmpImagePath, file_get_contents($url));

        $im = \imagecreatefromjpeg($tmpImagePath);
        imagepng($im, $path . '/' . $name . '.png');

        imagedestroy($im);
        unlink($tmpImagePath);
    }
}
