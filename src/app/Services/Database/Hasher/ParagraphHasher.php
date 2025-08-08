<?php

declare(strict_types=1);

namespace App\Services\Database\Hasher;

class ParagraphHasher
{
    public function getHash(string $content, string $type, string $prevBlockHash): string
    {
        // For separator
        if (empty($content)) {
            return $prevBlockHash;
        }

        return md5($content . '_' . $type);
    }

    public function getHashWithPosition(string $content, string $type, int $position): string
    {
        return md5($content . '_' . $type . '|' . $position);
    }
}
