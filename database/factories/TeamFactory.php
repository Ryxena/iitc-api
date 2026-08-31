<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'title' => fake()->sentence,
            'code' => fake()->bothify('??##??##'),
            'avatar' => fake()->imageUrl,
            'submission' => fake()->imageUrl,
            'leader_id' => User::factory(),
            'competition_id' => Competition::factory(),
        ];
    }
}
