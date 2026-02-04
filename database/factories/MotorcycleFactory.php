<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Motorcycle>
 */
class MotorcycleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'brand' => fake()->randomElement(['KTM', 'Honda', 'Yamaha', 'BMW', 'GasGas']),
        'model' => fake()->word() . ' ' . fake()->numberBetween(250, 1200),
        'year' => fake()->year(),
        'plate' => strtoupper(fake()->bothify('####???')),
        'current_km' => fake()->randomFloat(1, 1000, 50000),
        'photo' => null, // De moment sense foto
        ];
    }
}
