<?php

declare(strict_types=1);

namespace App\Enum\Translation;

enum ParagraphTypeEnum: string
{
    case HEADING = 'heading';
    case REGULAR = 'regular';
}
