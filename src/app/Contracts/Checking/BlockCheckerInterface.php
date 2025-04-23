<?php

declare(strict_types=1);

namespace App\Contracts\Checking;

use App\Blocks\ObjectBlock;
use App\Context\Checking\CheckingResult;

interface BlockCheckerInterface
{
    public function check(ObjectBlock $block): CheckingResult;
}
