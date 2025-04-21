<?php

declare(strict_types=1);

namespace App\Contracts\CommonMark;

use App\Blocks\ObjectBlock;
use Illuminate\Support\Collection;

interface ChildNodeObjectRendererInterface
{
    public function renderNodes(iterable $nodes): Collection;

    public function getBlockSeparator(): ObjectBlock;
}
