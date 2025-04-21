<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg\Inline;

use App\Blocks\Gutenberg\Image;
use App\Blocks\Gutenberg\LocalVideo;
use App\Blocks\GutenbergBlock;
use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;

class ImageRenderer implements BlockHtmlRendererInterface
{
    public function render(ObjectBlock $block, ChildBlockHtmlRendererInterface $childRenderer): GutenbergBlock
    {
        assert($block->getType() === BlockTypeEnum::IMAGE->value);

        $attributes = $block->getAttributes();
        $src = $attributes['external_url'] ?? $attributes['src'] ?? '';
        $alt = $attributes['alt'];
        $externalId = $attributes['external_id'] !== null ? intval($attributes['external_id']) : null;

        $fileUrl = $attributes['file_url'] ?? '';
        if (str_ends_with($fileUrl, '.m4v') || str_ends_with($fileUrl, '.webm')) {
            return new LocalVideo($fileUrl);
        }

        return new Image($src, $alt, $externalId);
    }
}
