<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Blocks\Gutenberg\Separator;
use App\Blocks\GutenbergBlock;
use App\Configuration\WordpressConfiguration;
use App\Services\Converters\ObjectToGutenberg\ObjectToGutenbergConverter;
use Illuminate\Support\Collection;

class GutenbergRenderer
{
    public function __construct(
        private ObjectToGutenbergConverter $converter,
        private WordpressConfiguration     $wordpressConfiguration,
    ) {}

    public function render(Collection $blocks): array
    {
        $rendered = [];

        $gutenbergBlocks = $this->converter->convert($blocks);

        $prevBlock = null;

        foreach ($gutenbergBlocks as $block) {
            /** @var GutenbergBlock $block */
            $rendered[] = $block->render($this->wordpressConfiguration);

            if ($prevBlock instanceof Separator && $block instanceof Separator) {
                continue;
            }

            $prevBlock = $block;
        }

        return $rendered;
    }
}
