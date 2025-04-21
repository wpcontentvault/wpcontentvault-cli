<?php

declare(strict_types=1);

namespace App\Services\Importing\Checksum;

class Checksum
{
    public static function calculate(string $html): string
    {
        $html = strip_tags($html);
        $content = mb_strtolower($html);

        $pattern = '/[^a-zA-Z0-9а-яА-ЯёЁ]/u';
        $content = preg_replace($pattern, '', $content);

        return md5($content);
    }
}
