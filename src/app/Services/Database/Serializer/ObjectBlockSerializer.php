<?php

declare(strict_types=1);

namespace App\Services\Database\Serializer;

use App\Blocks\ObjectBlock;
use App\Enum\BlockTypeEnum;
use App\Models\Article;

class ObjectBlockSerializer
{
    public function __construct(
        private ImageSerializer $imageSerializer,
    ) {}

    public function serializeBlock(ObjectBlock $block, Article $article): array
    {
        $data = $block->toArray();

        if ($block->getType() === BlockTypeEnum::IMAGE->value) {
            $image = $this->imageSerializer->serializeImage($block, $article);
            $data['value'] = $image->getKey();
        }

        $data['children'] = [];

        foreach ($block->getChildren() as $child) {
            $serialized = $this->serializeBlock($child, $article);

            $data['children'][] = $serialized;
        }

        return $data;
    }
}
