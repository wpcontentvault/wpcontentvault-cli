<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\DictionaryRecord;
use App\Models\Locale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class DictionaryRecordRepository
 *
 * @extends AbstractRepository<Category>
 */
class DictionaryRecordRepository extends AbstractRepository
{
    public function findRecord(Locale $source, Locale $target, string $text, ?string $context = null): ?DictionaryRecord
    {
        return $this->createQuery()
            ->where('source_id', $source->getKey())
            ->where('target_id', $target->getKey())
            ->where('source', $text)
            ->when(null !== $context, function (Builder $query) use ($context) {
                $query->where('context', $context);
            })
            ->first();
    }

    public function findAllByLocales(Locale $source, Locale $target): Collection
    {
        return $this->createQuery()
            ->where('source_id', $source->getKey())
            ->where('target_id', $target->getKey())
            ->get();
    }

    protected function getModelName(): string
    {
        return DictionaryRecord::class;
    }
}
