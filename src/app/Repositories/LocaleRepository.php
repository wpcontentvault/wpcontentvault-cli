<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Locale;
use Illuminate\Support\Collection;

/**
 * Class LocaleRepository
 *
 * @extends AbstractRepository<Locale>
 */
class LocaleRepository extends AbstractRepository
{
    public function getAllLocales(): Collection
    {
        return $this->createQuery()
            ->get();
    }

    public function findLocaleByCode(string $code): ?Locale
    {
        return $this->createQuery()
            ->where('code', $code)
            ->first();
    }

    protected function getModelName(): string
    {
        return Locale::class;
    }
}
