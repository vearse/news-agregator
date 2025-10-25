<?php

namespace Database\Factories;


use App\Enums\NewsSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $sources = NewsSource::values();
        
        return [
            'external_id' => fake()->unique()->uuid(),
            'source' => fake()->randomElement($sources),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'content' => fake()->paragraphs(5, true),
            'author' => fake()->name(),
            'category' => fake()->randomElement(['technology', 'business', 'sports', 'entertainment', 'science', 'health']),
            'url' => fake()->url(),
            'image_url' => fake()->imageUrl(640, 480, 'news'),
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function newsapi(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => NewsSource::NEWS_API->value,
        ]);
    }

    public function theguardian(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => NewsSource::THE_GUARDIAN->value,
        ]);
    }

    public function newsdataio(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => NewsSource::NEWS_DATA_IO->value,
        ]);
    }

    public function technology(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'technology',
        ]);
    }

    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'business',
        ]);
    }
}