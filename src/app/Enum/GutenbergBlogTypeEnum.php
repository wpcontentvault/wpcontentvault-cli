<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Traits\InteractsWithValues;

enum GutenbergBlogTypeEnum: string
{
    use InteractsWithValues;

    case HEADING = 'heading';
    case IMAGE = 'image';
    case QUOTE = 'quote';
    case LIST_BLOCK = 'list';
    case LIST_ITEM = 'list-item';
    case LOCAL_VIDEO = 'local-video';
    case CODE = 'code';
    case PARAGRAPH = 'paragraph';
    case SEPARATOR = 'separator';
    case TABLE = 'table';
    case YOUTUBE_VIDEO = 'youtube-video';
}
