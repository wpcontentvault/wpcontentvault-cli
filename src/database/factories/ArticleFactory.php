<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Locale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paragraph>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'path' => $this->faker->filePath(),
            'external_id' => $this->faker->randomNumber(8),
            'author' => $this->faker->name(),
            'thumbnail_url' => $this->faker->imageUrl(),
            'url' => $this->faker->url(),
        ];
    }

    public function locale(Locale $locale): static
    {
        return $this->state(fn(array $attributes) => [
            'locale_id' => $locale->getKey(),
        ]);
    }
}
