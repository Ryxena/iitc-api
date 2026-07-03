<?php

namespace Database\Factories;

use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName,
            'deadline' => fake()->dateTime,
            'max_members' => fake()->numberBetween(3, 7),
            'price' => fake()->numerify('#####'),
            'description' => fake()->text(250),
            'guide_book' => fake()->imageUrl,
            'cover' => fake()->imageUrl,
            'event_id' => \App\Models\Event::factory(),
        ];
    }
}
