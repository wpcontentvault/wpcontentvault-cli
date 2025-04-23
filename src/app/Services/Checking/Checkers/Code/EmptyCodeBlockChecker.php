<?php

declare(strict_types=1);

namespace App\Services\Checking\Checkers\Code;

use App\Blocks\ObjectBlock;
use App\Context\Checking\CheckingResult;
use App\Contracts\Checking\BlockCheckerInterface;

class EmptyCodeBlockChecker implements BlockCheckerInterface
{
    public function check(ObjectBlock $block): CheckingResult
    {
        $trimmedContent = trim(rtrim($block->getContent(), "\ \n\r\t\v\0"), "\ \n\r\t\v\0");

        $failed = false;

        if (empty($trimmedContent)) {
            $failed = true;
        }

        return new CheckingResult(
            block: $block,
            failed: $failed,
            message: "Empty Code Block",
        );
    }
}
