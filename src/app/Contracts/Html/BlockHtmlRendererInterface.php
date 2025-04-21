<?php

declare(strict_types=1);

namespace App\Contracts\Html;

use App\Blocks\ObjectBlock;

interface BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer);
}
