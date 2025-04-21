<?php

declare(strict_types=1);

namespace App\Services\Database\Deserializer;

use App\Models\Article;
use App\Models\Locale;
use Illuminate\Support\Collection;

class ArticleBlocksDeserializer
{
    public function __construct(
        private ObjectBlockDeserializer $blockDeserializer
    ) {}

    public function deserialize(Article $article, ?Locale $locale = null): Collection
    {
        $collection = collect();

        foreach ($article->content as $block) {
            $collection->add($this->blockDeserializer->deserializeBlock($block, $article, $locale));
        }

        return $collection;
    }
}
