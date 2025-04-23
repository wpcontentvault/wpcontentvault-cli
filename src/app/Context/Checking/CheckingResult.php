<?php

declare(strict_types=1);

namespace App\Context\Checking;

use App\Blocks\ObjectBlock;

class CheckingResult
{
    public function __construct(
        public readonly ObjectBlock $block,
        public readonly bool        $failed = false,
        public readonly string      $message = '',
    ) {}
}
