<?php

namespace Database\Factories;

use App\Models\Flock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Flock>
 */
class FlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'animal_type_id' => \App\Models\AnimalType::factory(),
            'building_id' => \App\Models\Building::factory(),
            'arrival_date' => $this->faker->dateTime(),
            'initial_quantity' => $this->faker->numberBetween(10, 1000),
            'current_quantity' => $this->faker->numberBetween(10, 1000),
            'purchase_cost' => $this->faker->numberBetween(100, 10000),
            'status' => 'active',
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
