<?php

declare(strict_types=1);

namespace App\Blocks\Object\Table;

use App\Blocks\ObjectBlock;
use App\Enum\BlockTypeEnum;

class TableSectionHeadObject extends ObjectBlock
{
    public function getType(): string
    {
        return BlockTypeEnum::TABLE_SECTION_HEAD->value;
    }
}
