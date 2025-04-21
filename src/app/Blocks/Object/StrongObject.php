<?php

declare(strict_types=1);

namespace App\Blocks\Object;

use App\Blocks\ObjectBlock;
use App\Enum\BlockTypeEnum;

class StrongObject extends ObjectBlock
{
    public function getType(): string
    {
        return BlockTypeEnum::STRONG->value;
    }
}
