<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->randomElement(['Anime', 'Drama', 'Manga', 'Light Novel']);
        $isVideo = in_array($category, ['Anime', 'Drama'], true);

        return [
            'title' => fake()->unique()->words(3, true),
            'category' => $category,
            'genre' => fake()->randomElement(['Action', 'Fantasy', 'Romance', 'Sports', 'Mystery']),
            'description' => fake()->sentence(),
            'episodes' => $isVideo ? fake()->numberBetween(1, 50) : null,
            'format' => $isVideo ? 'Streaming' : 'Digital',
        ];
    }
}
