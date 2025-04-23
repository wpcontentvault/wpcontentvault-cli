<?php

declare(strict_types=1);

namespace App\Services\Checking\Checkers\Code;

use App\Blocks\ObjectBlock;
use App\Context\Checking\CheckingResult;
use App\Contracts\Checking\BlockCheckerInterface;

class ContainsDollarSignChecker implements BlockCheckerInterface
{
    public function check(ObjectBlock $block): CheckingResult
    {
        $failed = false;

        if (preg_match("/\n\\$ /u", $block->getContent())) {
            $failed = true;
        }

        return new CheckingResult(
            block: $block,
            failed: $failed,
            message: "Contains dollar sign",
        );
    }
}
