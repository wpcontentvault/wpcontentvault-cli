<?php

declare(strict_types=1);

namespace App\Services\Database\Dictionary;

use App\Models\DictionaryRecord;
use App\Models\Locale;
use App\Repositories\DictionaryRecordRepository;
use App\Repositories\LocaleRepository;
use App\Services\AI\EmbeddingsService;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;

class DictionaryLoader
{
    public function __construct(
        private VaultConfigLoader          $loader,
        private VaultPathResolver          $pathResolver,
        private LocaleRepository           $locales,
        private DictionaryRecordRepository $dictionaryRecords,
        private EmbeddingsService          $embeddingService,
    ) {}

    public function loadDictionariesFromConfig(): void
    {
        $localesList = $this->locales->getAllLocales()->keyBy('code');
        $dictionaryConfig = $this->loader->loadFromPath($this->pathResolver->getRoot(), 'dictionary.json');

        foreach ($dictionaryConfig as $dictionary) {
            $sourceLocale = $localesList->get($dictionary['source']);
            if (null === $sourceLocale) {
                throw new \RuntimeException("Locale {$dictionary['source']} not found!");
            }
            $targetLocale = $localesList->get($dictionary['target']);
            if (null === $targetLocale) {
                throw new \RuntimeException("Locale {$dictionary['target']} not found!");
            }

            $this->processDictionary($sourceLocale, $targetLocale, $dictionary['translations']);
        }
    }

    private function processDictionary(Locale $source, Locale $target, array $translations): void
    {
        foreach ($translations as $translationInfo) {
            $record = $this->dictionaryRecords->findRecord(
                $source,
                $target,
                $translationInfo['source'],
                $translationInfo['context'] ?? null,
            );

            if (null === $record) {
                $record = new DictionaryRecord();
                $record->sourceLocale()->associate($source);
                $record->targetLocale()->associate($target);
                $record->source = $translationInfo['source'];
                $record->context = $translationInfo['context'] ?? null;
                $record->embedding = $this->embeddingService->embeddings($translationInfo['source']);
            }

            $record->translation = $translationInfo['target'];
            $record->save();
        }
    }
}
