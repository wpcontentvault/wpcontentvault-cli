<?php

declare(strict_types=1);

namespace App\Registry;

use App\Blocks\Object\CodeObject;
use App\Blocks\Object\EmphasisObject;
use App\Blocks\Object\HeadingObject;
use App\Blocks\Object\ImageObject;
use App\Blocks\Object\LinkObject;
use App\Blocks\Object\ListItemObject;
use App\Blocks\Object\ListObject;
use App\Blocks\Object\NewLineObject;
use App\Blocks\Object\ParagraphObject;
use App\Blocks\Object\QuoteObject;
use App\Blocks\Object\StrongObject;
use App\Blocks\Object\Table\TableCellObject;
use App\Blocks\Object\Table\TableHeaderCellObject;
use App\Blocks\Object\Table\TableObject;
use App\Blocks\Object\Table\TableRowObject;
use App\Blocks\Object\Table\TableSectionBodyObject;
use App\Blocks\Object\Table\TableSectionHeadObject;
use App\Blocks\Object\TextObject;
use App\Enum\BlockTypeEnum;
use RuntimeException;

class ObjectBlockRegistry
{
    private array $blocks = [
        BlockTypeEnum::PARAGRAPH->value => ParagraphObject::class,
        BlockTypeEnum::HEADING->value => HeadingObject::class,
        BlockTypeEnum::TEXT->value => TextObject::class,
        BlockTypeEnum::IMAGE->value => ImageObject::class,
        BlockTypeEnum::CODE->value => CodeObject::class,
        BlockTypeEnum::QUOTE->value => QuoteObject::class,
        BlockTypeEnum::NEWLINE->value => NewlineObject::class,
        BlockTypeEnum::STRONG->value => StrongObject::class,
        BlockTypeEnum::LINK->value => LinkObject::class,
        BlockTypeEnum::LIST->value => ListObject::class,
        BlockTypeEnum::LIST_ITEM->value => ListItemObject::class,
        BlockTypeEnum::EMPHASIS->value => EmphasisObject::class,
        BlockTypeEnum::TABLE->value => TableObject::class,
        BlockTypeEnum::TABLE_HEADER_CELL->value => TableHeaderCellObject::class,
        BlockTypeEnum::TABLE_CELL->value => TableCellObject::class,
        BlockTypeEnum::TABLE_ROW->value => TableRowObject::class,
        BlockTypeEnum::TABLE_SECTION_HEAD->value => TableSectionHeadObject::class,
        BlockTypeEnum::TABLE_SECTION_BODY->value => TableSectionBodyObject::class,
        BlockTYpeEnum::VIDEO_LINK->value => LinkObject::class,
    ];

    public function registerBlock(string $type, string $className): void
    {
        $this->blocks[$type] = $className;
    }

    public function getClassNameForType(string $type): string
    {
        if (isset($this->blocks[$type]) === false) {
            throw new RuntimeException(sprintf('Unknown block type "%s".', $type));
        }

        return $this->blocks[$type];
    }
}
