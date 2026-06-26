<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
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
            'cover' => fake()->imageUrl
        ];
    }
}
