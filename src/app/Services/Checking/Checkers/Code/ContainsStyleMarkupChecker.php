<?php

declare(strict_types=1);

namespace App\Services\Checking\Checkers\Code;

use App\Blocks\ObjectBlock;
use App\Context\Checking\CheckingResult;
use App\Contracts\Checking\BlockCheckerInterface;

class ContainsStyleMarkupChecker implements BlockCheckerInterface
{
    public function check(ObjectBlock $block): CheckingResult
    {
        $failed = false;

        if (str_contains($block->getContent(), "&lt;span style")) {
            $failed = true;
        }

        return new CheckingResult(
            block: $block,
            failed: $failed,
            message: "Contains style markup",
        );
    }
}
