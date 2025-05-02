<?php

declare(strict_types=1);

namespace App\Enum\Wordpress;

enum ImageLinkDestinationEnum: string
{
    case MEDIA = 'media';
    case NONE = 'none';
}
