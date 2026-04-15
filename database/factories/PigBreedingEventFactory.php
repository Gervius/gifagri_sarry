<?php

namespace Database\Factories;

use App\Models\PigBreedingEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PigBreedingEvent>
 */
class PigBreedingEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flock_id' => \App\Models\Flock::factory(),
            'event_type' => $this->faker->randomElement(['heat', 'mating', 'pregnancy_check', 'farrowing', 'weaning']),
            'event_date' => $this->faker->dateTimeBetween('-1 month'),
            'piglets_born_alive' => $this->faker->optional()->numberBetween(5, 12),
            'piglets_stillborn' => $this->faker->optional()->numberBetween(0, 2),
            'piglets_weaned' => $this->faker->optional()->numberBetween(4, 10),
            'boar_flock_id' => null,
        ];
    }
}
