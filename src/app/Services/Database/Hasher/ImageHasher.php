<?php

declare(strict_types=1);

namespace App\Services\Database\Hasher;

use App\Blocks\ObjectBlock;
use App\Enum\BlockTypeEnum;

class ImageHasher
{
    public function getHash(ObjectBlock $block): string
    {
        \assert($block->getType() === BlockTypeEnum::IMAGE->value);

        return md5($block->getAttributes()['src']);
    }
}
