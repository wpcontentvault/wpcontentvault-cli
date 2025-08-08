<?php

declare(strict_types=1);

namespace App\Services\Utils;

class StringUtils
{
    public static function containsCyrillic(string $text): bool
    {
        return (bool) preg_match('/[\p{Cyrillic}]/u', $text);
    }

    public static function removeImageSize(string $imageUrl): string
    {
        // delete scaled prefix
        $imageUrl = str_replace('-scaled.', '.', $imageUrl);
        $imageUrl = str_replace('-e1507053372297.', '.', $imageUrl);
        $imageUrl = str_replace('-e1530814241401.', '.', $imageUrl);
        //Need also clean this for image with specified size
        $imageUrl = str_replace('-scaled-', '-', $imageUrl);
        $imageUrl = str_replace('-e1530814241401-', '-', $imageUrl);
        $imageUrl = str_replace('-e1507053372297-', '-', $imageUrl);

        // delete size ex:1024x780
        $patternSrc = "/-\d+[Xx]\d+\./";
        $patternWithSize = "/-\d+[Xx]\d+-/";

        $imageUrl = preg_replace($patternSrc, '.', $imageUrl);
        $imageUrl = preg_replace($patternWithSize, '-', $imageUrl);

        return $imageUrl;
    }
}
