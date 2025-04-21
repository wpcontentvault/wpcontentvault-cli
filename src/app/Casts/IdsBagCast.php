<?php

declare(strict_types=1);

namespace App\Casts;

use App\Context\IdsBag;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class IdsBagCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): IdsBag
    {
        if (isset($attributes[$key])) {
            $ids = json_decode($attributes[$key]);
        } else {
            $ids = [];
        }

        return new IdsBag($ids);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, array>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (! $value instanceof IdsBag) {
            throw new InvalidArgumentException('The given value is not an IdsBag instance.');
        }

        return [
            $key => $value->toArray(),
        ];
    }
}
