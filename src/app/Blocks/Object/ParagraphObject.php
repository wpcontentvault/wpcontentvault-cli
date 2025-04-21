<?php

declare(strict_types=1);

namespace App\Blocks\Object;

use App\Blocks\ObjectBlock;
use App\Enum\BlockTypeEnum;

class ParagraphObject extends ObjectBlock
{
    public function getType(): string
    {
        return BlockTypeEnum::PARAGRAPH->value;
    }
}
