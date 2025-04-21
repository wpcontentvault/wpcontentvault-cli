<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Blocks\Gutenberg\Separator;
use App\Blocks\GutenbergBlock;
use App\Services\Converters\ObjectToGutenberg\ObjectToGutenbergConverter;
use Illuminate\Support\Collection;

class GutenbergRenderer
{
    public function __construct(
        private ObjectToGutenbergConverter $converter
    ) {}

    public function render(Collection $blocks): array
    {
        $rendered = [];

        $gutenbergBlocks = $this->converter->convert($blocks);

        $prevBlock = null;

        foreach ($gutenbergBlocks as $block) {
            /** @var GutenbergBlock $block */
            $rendered[] = $block->render();

            if ($prevBlock instanceof Separator && $block instanceof Separator) {
                continue;
            }

            $prevBlock = $block;
        }

        return $rendered;
    }
}
