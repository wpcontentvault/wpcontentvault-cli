<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractRepository
 *
 * @template T of \Illuminate\Database\Eloquent\Model
 */
abstract class AbstractRepository
{
    /**
     * @return class-string<T>
     */
    abstract protected function getModelName(): string;

    /**
     * @return Builder<T>
     */
    public function createQuery(): Builder
    {
        return $this->getModelName()::query();
    }

    /**
     * @return T
     */
    public function createModel(): Model
    {
        $modelName = $this->getModelName();

        return new $modelName;
    }
}
