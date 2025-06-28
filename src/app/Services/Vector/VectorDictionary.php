<?php

declare(strict_types=1);

namespace App\Services\Vector;

use App\Models\DictionaryRecord;
use Edgaras\StrSim\Cosine;
use Illuminate\Support\Collection;

class VectorDictionary
{

    private function __construct(
        private array $data = [],
    ) {}

    public
    static function createFromCollection(Collection $collection): VectorDictionary
    {
        $data = [];

        foreach ($collection as $item) {
            /** @var DictionaryRecord $item */
            $data[$item->id] = $item->embedding;
        }

        return new VectorDictionary($data);
    }

    public function searchSimilar(array $embeddings): array
    {
        $similarityData = [];

        foreach ($this->data as $key => $value) {
            $similarity = Cosine::similarityFromVectors(
                $embeddings,
                $value
            );
            if ($similarity > 0.4) {
                $similarityData[$key] = $similarity;
            }
        }

        arsort($similarityData);
        $ids = array_keys($similarityData);

        return array_slice($ids, 0, 10);
    }
}
