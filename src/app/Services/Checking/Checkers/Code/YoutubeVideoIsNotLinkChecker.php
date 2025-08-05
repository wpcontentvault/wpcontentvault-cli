<?php

declare(strict_types=1);

namespace App\Services\Checking\Checkers\Code;

use App\Blocks\ObjectBlock;
use App\Context\Checking\CheckingResult;
use App\Contracts\Checking\BlockCheckerInterface;

class YoutubeVideoIsNotLinkChecker implements BlockCheckerInterface
{
    public function check(ObjectBlock $block): CheckingResult
    {
        if (null !== $block->getContent()) {
            $trimmedContent = trim(rtrim($block->getContent(), "\ \n\r\t\v\0"), "\ \n\r\t\v\0");
        } else {
            $trimmedContent = "";
        }

        $failed = false;

        if (preg_match("/^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})(?:\S+)?$/u", $trimmedContent)) {
            $failed = true;
        }

        return new CheckingResult(
            block: $block,
            failed: $failed,
            message: "Contains plain youtube link.",
        );
    }
}
