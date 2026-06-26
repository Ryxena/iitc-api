<?php

namespace Database\Factories;

use App\Models\CategoryCompetition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryCompetition>
 */
class CategoryCompetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'competition_id' => fake()->numberBetween(1, 10),
            'category_id' => fake()->numberBetween(1, 2),
        ];
    }
}
