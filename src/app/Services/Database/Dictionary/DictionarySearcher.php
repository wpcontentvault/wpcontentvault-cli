<?php

declare(strict_types=1);

namespace App\Services\Database\Dictionary;

use App\Models\DictionaryRecord;
use App\Models\Locale;
use App\Repositories\DictionaryRecordRepository;
use App\Services\AI\EmbeddingsService;
use App\Services\Vector\VectorDictionary;

class DictionarySearcher
{
    private array $dictionaries = [];

    public function __construct(
        private EmbeddingsService          $embeddingService,
        private DictionaryRecordRepository $dictionaryRecords,
    ) {}

    public function getTranslationRecommendations(Locale $source, Locale $target, string $text): array
    {
        $embeddings = $this->embeddingService->embeddings($text);
        $vectorDictionary = $this->resolveDictionary($source, $target);

        $similarIds = $vectorDictionary->searchSimilar($embeddings);

        $found = $this->dictionaryRecords->createQuery()
            ->whereIn('id', $similarIds)
            ->get()
            ->sortBy(function (DictionaryRecord $dictionaryRecord) use ($similarIds) {
                return array_search($dictionaryRecord->getKey(), $similarIds, true) ?? 9999;
            });

        $suggestions = collect();

        foreach ($found as $item) {
            if (null !== $item->context) {
                $suggestions->push($item->source . ' (' . $item->context . ') => ' . $item->translation);
            } else {
                $suggestions->push($item->source . ' => ' . $item->translation);
            }
        }

        return $suggestions->toArray();
    }

    private function resolveDictionary(Locale $source, Locale $target): VectorDictionary
    {
        $code = $source->code . '_' . $target->code;

        if (false === isset($this->dictionaries[$code])) {
            $translations = $this->dictionaryRecords->findAllByLocales($source, $target);
            $vectorDictionary = VectorDictionary::createFromCollection($translations);

            $this->dictionaries[$code] = $vectorDictionary;
        }

        return $this->dictionaries[$code];
    }
}
