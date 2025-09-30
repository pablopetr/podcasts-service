<?php

namespace Database\Factories;

use App\Models\Show;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'show_id' => Show::factory(),
            'title' => fake()->sentence(6),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraphs(3, true),
            'duration_sec' => fake()->numberBetween(300, 7200), // Duration between 5 minutes and 2 hours
            'audio_url' => fake()->url() . '/audio/' . fake()->uuid() . '.mp3',
            'published_at' => fake()->dateTimeBetween('-1 years', 'now'),
        ];
    }
}
