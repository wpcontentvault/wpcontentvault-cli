<?php

declare(strict_types=1);

namespace App\Enum\Wordpress;

enum ArticleStatusEnum: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'publish';
    case PENDING = 'pending';
    case FUTURE = 'future';
    case PRIVATE = 'private';
    case TRASH = 'trash';
}
