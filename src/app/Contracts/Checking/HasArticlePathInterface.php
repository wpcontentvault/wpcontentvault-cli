<?php

declare(strict_types=1);

namespace App\Contracts\Checking;

use App\Models\Article;

interface HasArticlePathInterface
{
    public function setArticlePath(string $path);

    public function getArticlePath();
}
