<?php

declare(strict_types=1);

namespace App\Contracts\Html;

use App\Blocks\ObjectBlock;

interface ChildBlockHtmlRendererInterface
{
    public function renderNodes(iterable $nodes): string;

    public function renderNode(ObjectBlock $node);
}
