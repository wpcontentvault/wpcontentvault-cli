<?php

declare(strict_types=1);

namespace App\Services\Utils;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class LinkChecker
{
    public static function isLinkValid(string $url): bool
    {
        try {
            $response = Http::head($url);

            if ($response->successful()) {
                return true;
            }

            return false;
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function isUrl(string $text): bool
    {
        return filter_var($text, FILTER_VALIDATE_URL) !== false;
    }

    public static function isYoutubeURL(string $url): bool
    {
        // Let's check the host first
        $parse = parse_url($url);
        $host = $parse['host'] ?? null;

        if ($host === null) {
            throw new RuntimeException("Invalid link $url");
        }

        if (! in_array($host, ['youtube.com', 'www.youtube.com', 'youtu.be'])) {
            return false;
        }

        return true;
    }
}
