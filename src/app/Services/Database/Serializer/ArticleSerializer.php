<?php

declare(strict_types=1);

namespace App\Services\Database\Serializer;

use App\Models\Article;
use App\Services\Database\Cleaner\ImageCleaner;
use Illuminate\Support\Collection;

class ArticleSerializer
{
    public function __construct(
        private ObjectBlockSerializer $blockSerializer,
        private ImageCleaner $imageCleaner,
    ) {}

    public function serializeArticle(Collection $blocks, Article $article): void
    {
        $oldImageIds = $article->image_ids->toArray();

        $article->image_ids->clear();

        $serialized = [];

        foreach ($blocks as $block) {
            $serialized[] = $this->blockSerializer->serializeBlock($block, $article);
        }

        $article->content = $serialized;
        $article->save();

        $newImageIds = $article->image_ids->toArray();

        $removedImages = array_diff($oldImageIds, $newImageIds);

        $this->imageCleaner->markImagesAsStale($removedImages);
    }
}
