<?php

declare(strict_types=1);

namespace App\Casts;

use App\Context\LocaleOptions;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class LocaleOptionsCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): LocaleOptions
    {
        if (isset($attributes[$key])) {
            $options = json_decode($attributes[$key], true);
        } else {
            $options = [];
        }

        return new LocaleOptions(
            shouldCapitalizeTitle: $options['should_capitalize_title'] ?? false,
            shouldHaveTranslatedByAiLabel: $options['should_have_translated_by_ai_label'] ?? false,
            customConsolationsLabel: $options['custom_consolations_label'] ?? null
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, array>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (! $value instanceof LocaleOptions) {
            throw new InvalidArgumentException('The given value is not an LocaleOptions instance.');
        }

        return [
            $key => [
                'should_capitalize_title' => $value->shouldCapitalizeTitle,
                'should_have_translated_by_ai_label' => $value->shouldHaveTranslatedByAiLabel,
                'custom_consolations_label' => $value->customConsolationsLabel,
            ],
        ];
    }
}
