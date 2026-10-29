<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'year' => (string) fake()->year(),
            'role' => fake()->jobTitle(),
            'company' => fake()->company(),
            'description' => fake()->sentence(),
        ];
    }
}
