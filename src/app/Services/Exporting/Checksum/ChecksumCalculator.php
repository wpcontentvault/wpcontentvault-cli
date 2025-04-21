<?php

declare(strict_types=1);

namespace App\Services\Exporting\Checksum;

use App\Blocks\GutenbergBlock;

class ChecksumCalculator
{
    public function calculateChecksumForBlocks(array $blocks): string
    {
        $string = '';

        foreach ($blocks as $block) {
            /** @var GutenbergBlock $block */
            $string .= print_r($block, true);
        }

        return md5($string);
    }
}
