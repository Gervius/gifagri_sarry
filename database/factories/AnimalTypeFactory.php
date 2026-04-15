<?php

namespace Database\Factories;

use App\Models\AnimalType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnimalType>
 */
class AnimalTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'code' => strtoupper($this->faker->unique()->lexify('??')),
            'can_lay_eggs' => $this->faker->boolean,
            'has_growth_tracking' => $this->faker->boolean,
            'has_breeding_cycle' => $this->faker->boolean,
        ];
    }
}
