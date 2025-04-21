<?php

declare(strict_types=1);

namespace App\Services\Database\Deserializer;

use App\Blocks\ObjectBlock;
use App\Enum\BlockTypeEnum;
use App\Models\Article;
use App\Models\Locale;
use App\Registry\ObjectBlockRegistry;

class ObjectBlockDeserializer
{
    public function __construct(
        private ImageDeserializer $imageDeserializer,
        private ObjectBlockRegistry $blockRegistry,
    ) {}

    public function deserializeBlock(array $data, Article $article, ?Locale $locale = null): ObjectBlock
    {
        if ($data['type'] === BlockTypeEnum::IMAGE->value) {
            return $this->imageDeserializer->deserializeImage($data, $article);
        }

        $children = collect();

        foreach ($data['children'] as $child) {
            $children->add($this->deserializeBlock($child, $article, $locale));
        }

        $class = $this->blockRegistry->getClassNameForType($data['type']);

        return new $class(
            $data['attrs'],
            $children, $data['value']
        );
    }
}
