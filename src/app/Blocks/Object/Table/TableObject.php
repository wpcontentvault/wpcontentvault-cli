<?php

declare(strict_types=1);

namespace App\Blocks\Object\Table;

use App\Blocks\ObjectBlock;
use App\Enum\BlockTypeEnum;

class TableObject extends ObjectBlock
{
    public function getType(): string
    {
        return BlockTypeEnum::TABLE->value;
    }
}
