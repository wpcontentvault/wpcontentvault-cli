<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Traits\InteractsWithValues;

enum BlockTypeEnum: string
{
    use InteractsWithValues;

    case PARAGRAPH = 'paragraph';
    case HEADING = 'heading';
    case TEXT = 'text';
    case IMAGE = 'image';
    case CODE = 'code';
    case QUOTE = 'quote';
    case NEWLINE = 'newline';
    case STRONG = 'strong';
    case LINK = 'link';
    case LIST = 'list';
    case LIST_ITEM = 'list_item';
    case EMPHASIS = 'emphasis';
    case TABLE = 'table';
    case TABLE_HEADER_CELL = 'table_header_cell';
    case TABLE_CELL = 'table_cell';
    case TABLE_ROW = 'table_row';
    case TABLE_SECTION_HEAD = 'table_section_head';
    case TABLE_SECTION_BODY = 'table_section_body';
    case VIDEO_LINK = 'video_link';
}
