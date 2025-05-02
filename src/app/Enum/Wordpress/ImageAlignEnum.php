<?php

declare(strict_types=1);

namespace App\Enum\Wordpress;

enum ImageAlignEnum: string
{
    case NONE = 'none';
    case LEFT = 'left';
    case CENTER = 'center';
    case RIGHT = 'right';
    case WIDE = 'wide';
    case FULL = 'full';
}
