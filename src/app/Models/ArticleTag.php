<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleTag extends Pivot
{
    public $table = 'article_tags';
}
