<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\GutenbergBlogTypeEnum;
use App\Models\Article;
use App\Models\Locale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paragraph>
 */
class ParagraphFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = $this->faker->realText(1024);

        return [
            'content' => $content,
            'hash' => md5($content),
            'is_stale' => false,
        ];
    }

    public function type(GutenbergBlogTypeEnum $type): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => $type->value,
        ]);
    }

    public function stale(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_stale' => true,
        ]);
    }

    public function order(int $order): static
    {
        return $this->state(fn(array $attributes) => [
            'order' => $order,
        ]);
    }

    public function article(Article $article): static
    {
        return $this->state(fn(array $attributes) => [
            'article_id' => $article->getKey(),
        ]);
    }
}
