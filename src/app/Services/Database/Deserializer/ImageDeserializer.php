<?php

declare(strict_types=1);

namespace App\Services\Database\Deserializer;

use App\Blocks\ObjectBlock;
use App\Models\Article;
use App\Registry\ObjectBlockRegistry;
use App\Repositories\ImageRepository;
use RuntimeException;

class ImageDeserializer
{
    public function __construct(
        private ImageRepository $images,
        private ObjectBlockRegistry $blockRegistry,
    ) {}

    public function deserializeImage(array $data, Article $article): ObjectBlock
    {
        $image = $this->images->findImageByUuidAndArticle($data['value'], $article);

        if ($image == null) {
            throw new RuntimeException("Can't deserialize image!");
        }

        if ($image->external_url == null) {
            throw new RuntimeException("Can't deserialize image, external url is empty!");
        }

        $data['attrs']['external_id'] = $image->external_id;
        $data['attrs']['external_url'] = $image->thumbnail_url;
        $data['attrs']['file_url'] = $image->external_url;

        $class = $this->blockRegistry->getClassNameForType($data['type']);

        return new $class(
            $data['attrs'],
            collect(),
            $data['value'],
            $image->getKey()
        );
    }
}
