<?php

declare(strict_types=1);

namespace App\Contracts\CommonMark;

use League\CommonMark\Node\Node;

interface NodeObjectRendererInterface
{
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer);
}
