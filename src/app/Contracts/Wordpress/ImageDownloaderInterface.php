<?php

declare(strict_types=1);

namespace App\Contracts\Wordpress;

interface ImageDownloaderInterface
{
    public function downloadMedia(string $url, string $path): string;
}
